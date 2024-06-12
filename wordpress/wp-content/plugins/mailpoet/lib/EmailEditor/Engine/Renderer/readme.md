# MailPoet Email Renderer

The renderer is WIP and so is the API for adding support email rendering for new blocks.

## Adding support for a core block

1. Add block into `ALLOWED_BLOCK_TYPES` in `mailpoet/lib/EmailEditor/Engine/Renderer/SettingsController.php`.
2. Make sure the block is registered in the editor. Currently all core blocks are registered in the editor.
3. Add BlockRender class (e.g. Heading) into `mailpoet/lib/EmailEditor/Integration/Core/Renderer/Blocks` folder. <br />

```php
<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks;

use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\BlockRenderer;use MailPoet\EmailEditor\Engine\SettingsController;

class Heading implements BlockRenderer {
  public function render($blockContent, array $parsedBlock, SettingsController $settingsController): string {
    return 'HEADING_BLOCK'; // here comes your rendering logic;
  }
}
```

4. Register the renderer

```php
<?php

use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\BlocksRegistry;

add_action('mailpoet_blocks_renderer_initialized', 'register_my_block_email_renderer');

function register_my_block_email_renderer(BlocksRegistry $blocksRegistry): void {
  $blocksRegistry->addBlockRenderer('core/heading', new Renderer\Blocks\Heading());
}
```

Note: For core blocks this is currently done in `MailPoet\EmailEditor\Integrations\Core\Initializer`.

5. Implement the rendering logic in the renderer class.

## Tips for adding support for block

- You can take inspiration on block rendering from MJML in the https://mjml.io/try-it-live
- Test the block in different clients [Litmus](https://litmus.com/)
- You can take some inspirations from the HTML renderer by the old email editor

## TODO

- add universal/fallback renderer for rendering blocks that are not covered by specialized renderers
- add support for all core blocks
- move the renderer to separate package
