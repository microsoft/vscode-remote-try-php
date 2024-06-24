/**
 * Astra's Live Search
 *
 * @package Astra
 * @since x.x.x
 */

(function () {
	function decodeHTMLEntities(string) {
		var doc = new DOMParser().parseFromString(string, "text/html");
		return doc.documentElement.textContent;
	}

	function getSearchResultPostMarkup(resultsData) {
		let processedHtml = "";

		Object.entries(resultsData).map(([postType, postsData]) => {
			let postTypeLabel = astra_search.search_post_types_labels[postType]
				? astra_search.search_post_types_labels[postType]
				: postType + "s";
			processedHtml += `<label class="ast-search--posttype-heading"> ${postTypeLabel} </label>`;
			postsData.map((post) => {
				const searchPostTitle = decodeHTMLEntities(post.title.rendered);
				const headerCoverSearch = document.getElementById("ast-search-form");
				const fullScreenSearch = document.getElementById("ast-seach-full-screen-form");
				if (fullScreenSearch || headerCoverSearch) {
					processedHtml += `<a class="ast-search-item" role="option" target="_self" href="${post.link}" tabindex="1"> <span> ${searchPostTitle} </span> </a>`;
				} else {
					processedHtml += `<a class="ast-search-item" role="option" target="_self" href="${post.link}"> <span> ${searchPostTitle} </span> </a>`;
				}
			});
		});

		return processedHtml;
	}

	window.addEventListener("load", function (e) {
		const searchInputs = document.querySelectorAll(".search-field");
		searchInputs.forEach((searchInput) => {
			searchInput.addEventListener("input", function (event) {
				const searchForm = searchInput.closest("form.search-form");
				const searchTerm = event.target.value.trim();
				const postTypes = astra_search.search_page_condition ? astra_search.search_page_post_types : astra_search.search_post_types;

				const searchResultsWrappers = document.querySelectorAll(
					".ast-live-search-results"
				);
				if (searchResultsWrappers) {
					searchResultsWrappers.forEach(function (wrap) {
						wrap.parentNode.removeChild(wrap);
					});
				}

				try {
					const restRequest = `${
						astra_search.rest_api_url
					}wp/v2/posts${
						astra_search.rest_api_url.indexOf("?") > -1 ? "&" : "?"
					}_embed=1&post_type=ast_queried:${postTypes.join(
						":"
					)}&per_page=${
						astra_search.search_posts_per_page
					}&search=${searchTerm}${
						astra_search.search_language
							? `&lang=${astra_search.search_language}`
							: ""
					}`;

					var xhr = new XMLHttpRequest();
					xhr.open("GET", restRequest, true);
					xhr.onreadystatechange = function () {
						if (xhr.readyState === 4 && xhr.status === 200) {
							const postsData = JSON.parse(xhr.responseText);
							let resultsContainer = "";

							if (postsData.length > 0) {
								let formattedPostsData = {};
								postsData.map((post) => {
									if (post.type in formattedPostsData) {
										formattedPostsData[post.type].push(
											post
										);
									} else {
										formattedPostsData[post.type] = [post];
									}
								});
								let searchResultMarkup =
									getSearchResultPostMarkup(
										formattedPostsData
									);
								resultsContainer = `
									<div
										class="ast-live-search-results"
										role="listbox"
										aria-label="Search results"
										style="top: ${parseInt(searchForm.offsetHeight) + 10}px;"
									>
										${searchResultMarkup}
									</div>
								`;
							} else {
								resultsContainer = `
									<div
										class="ast-live-search-results"
										role="listbox"
										aria-label="Search results"
										style="top: ${parseInt(searchForm.offsetHeight) + 10}px;"
									>
										<label class="ast-search--no-results-heading"> ${
											astra_search.no_live_results_found
										} </label>
									</div>
								`;
							}

							const searchResultsWrappers =
								document.querySelectorAll(
									".ast-live-search-results"
								);
							if (searchResultsWrappers) {
								searchResultsWrappers.forEach(function (wrap) {
									wrap.parentNode.removeChild(wrap);
								});
							}
							searchForm.insertAdjacentHTML(
								"beforeend",
								resultsContainer
							);
						}
					};

					xhr.send();
				} catch (error) {
					console.error("Error while fetching data:", error);
				}
			});
		});
	});

	// Add a click event listener to the document.
	document.addEventListener("click", function (event) {
		const searchForm = event.target.closest("form.search-form");

		// Check if the clicked element is the search bar or the results dropdown
		if (null !== searchForm) {
			// Clicked inside the search bar or dropdown, do nothing
			if (searchForm.querySelector(".ast-live-search-results")) {
				searchForm.querySelector(
					".ast-live-search-results"
				).style.display = "block";
			}
		} else {
			// Clicked outside the search bar and dropdown, hide the dropdown
			const searchResultsWrappers = document.querySelectorAll(
				".ast-live-search-results"
			);
			if (searchResultsWrappers) {
				searchResultsWrappers.forEach(function (wrap) {
					wrap.style.display = "none";
				});
			}
		}
	});
})();
