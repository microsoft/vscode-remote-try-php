<!-- For now we follow Gutenberg's package changelog format https://github.com/WordPress/gutenberg/tree/HEAD/packages#maintaining-changelogs. -->

## 2.0.0 (2021-05-19)

### Breaking Changes

- Removed `...AbstractChainedJob::filter_items_before_processing` method in favor of the more flexible `process_items` method ([#2](https://github.com/woocommerce/action-scheduler-job-framework/pull/2))

### New Feature
- Added `...AbstractChainedJob::process_items` method to give jobs more control over how the whole batch is processed ([#2](https://github.com/woocommerce/action-scheduler-job-framework/pull/2))
