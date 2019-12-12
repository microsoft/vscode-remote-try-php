# Try Out Development Containers: PHP

This is a sample project that lets you try out the **[VS Code Remote - Containers](https://aka.ms/vscode-remote/containers)** extension in a few easy steps.

> **Note:** If you're following the quick start, you can jump to the [Things to try](#things-to-try) section. 

## Setting up the development container

Follow these steps to open this sample in a container:

1. If this is your first time using a development container, please follow the [getting started steps](https://aka.ms/vscode-remote/containers/getting-started).

2. To use this repository, you can either open a locally cloned copy of the code:

   - Clone this repository to your local filesystem.
   - Press <kbd>F1</kbd> and select the **Remote-Containers: Open Folder in Container...** command.
   - Select the cloned copy of this folder, wait for the container to start, and try things out!

   Or open the repository in an isolated Docker volume:

    - Press <kbd>F1</kbd> and select the **Remote-Containers: Try a Sample...** command.
    - Choose the "PHP" sample, wait for the container to start and try things out!
        > **Note:** Under the hood, this will use **Remote-Containers: Open Repository in Container...** command to clone the source code in a Docker volume instead of the local filesystem.

## Things to try

Once you have this sample opened in a container, you'll be able to work with it like you would locally.

> **Note:** This container runs as a non-root user with sudo access by default. Comment out `"remoteUser": "vscode"` in `.devcontainer/devcontainer.json` if you'd prefer to run as root.

Some things to try:

1. **Edit:**
   - Open `index.php`
   - Try adding some code and check out the language features.
1. **Terminal:** Press <kbd>ctrl</kbd>+<kbd>shift</kbd>+<kbd>\`</kbd> and type `uname` and other Linux commands from the terminal window.
1. **Build, Run, and Debug:**
   - Open `index.php`
   - Add a breakpoint (e.g. on line 4).
   - Press <kbd>F5</kbd> to launch the app in the container.
   - Once the breakpoint is hit, try hovering over variables, examining locals, and more.
1. **Running a server:**
   - From the terminal, run `php -S localhost:8000`
   - Press <kbd>F1</kbd> and run the **Remote-Containers: Forward Port from Container...** command.
   - Select port `8000`.
   - Click "Open Browser" in the notification that appears to access the web app on this new port.
   - Look back at the terminal, and you should see the output from your site navigations
   - Edit the text on line 21 in `index.php` and refresh the page to see the changes immediately take affect
 
## Contributing

This project welcomes contributions and suggestions. Most contributions require you to agree to a
Contributor License Agreement (CLA) declaring that you have the right to, and actually do, grant us
the rights to use your contribution. For details, visit https://cla.microsoft.com.

When you submit a pull request, a CLA-bot will automatically determine whether you need to provide
a CLA and decorate the PR appropriately (e.g., label, comment). Simply follow the instructions
provided by the bot. You will only need to do this once across all repos using our CLA.

This project has adopted the [Microsoft Open Source Code of Conduct](https://opensource.microsoft.com/codeofconduct/).
For more information see the [Code of Conduct FAQ](https://opensource.microsoft.com/codeofconduct/faq/) or
contact [opencode@microsoft.com](mailto:opencode@microsoft.com) with any additional questions or comments.

## License

Copyright © Microsoft Corporation All rights reserved.<br />
Licensed under the MIT License. See LICENSE in the project root for license information.
