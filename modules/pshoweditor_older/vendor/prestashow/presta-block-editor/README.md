# PrestaShow Block Editor

## Installation

Install package using `composer require prestashow/presta-block-editor`

## Usage 

Load all required CSS and JS files in your module class or controller and init editor.
```php
BlockEditor::loadBO($moduleInstance, $optionalEditorConfiguration);
```
This will convert every textarea with attribute `data-pshow-block-editor` into block editor.

Or you can specify id of textarea to be converted into block editor by adding to the configuration:
```php
BlockEditor::loadBO($moduleInstance, [
    'editors_id' => ['#my-textarea-id']
]);
```

Configuration options available here: https://imperavi.com/article/docs/settings/

---

Additionally you can add `BlockEditor::loadFO()` in the module main class in hookDisplayHeader() to load CSS for front-office.

## Examples

### Example 1. Default configuration

```php
<?php
// src/Controller/Admin/IndexController.php

namespace Prestashow\PShowNull\Controller\Admin;

use Prestashow\PrestaBlockEditor\BlockEditor;

class IndexController extends AbstractAdminController {

    public function indexAction(): void
    {
        BlockEditor::loadBO($this->module);
    }

}
```

```html
<!-- views/templates/admin/main_index.tpl -->

<textarea data-pshow-block-editor>
    <p>hello</p>
</textarea>
```

### Example 2. Custom block `card`

```php
<?php
// src/Controller/Admin/IndexController.php

namespace Prestashow\PShowNull\Controller\Admin;

use Prestashow\PrestaBlockEditor\BlockEditor;

class IndexController extends AbstractAdminController {

    public function indexAction(): void
    {     
        BlockEditor::loadBO($this->module, [
            'editors_id' => ['#my-textarea-id'],
            'addbarAdd' => ['card'],
            'card' => [
                'classname' => 'card',
                'template' => '<div class="card">...</div>'
            ],
        ]);
    }

}
```

```html
<!-- views/templates/admin/main_index.tpl -->

<textarea id="my-textarea-id">
    <p>hello</p>
</textarea>
```

### Example 3. Init editor by JS

```php
<?php
// src/Controller/Admin/IndexController.php

namespace Prestashow\PShowNull\Controller\Admin;

use Prestashow\PrestaBlockEditor\BlockEditor;

class IndexController extends AbstractAdminController {

    public function indexAction(): void
    {     
        BlockEditor::loadBO($this->module);
    }

}
```

```html
<!-- views/templates/admin/main_index.tpl -->

<textarea id="my-textarea-id">
    <p>hello</p>
</textarea>

<script>
    PShowBlockEditor('#my-textarea-id', {
        addbarAdd: ['card'],
        card: {
            classname: 'card',
            template: '<div class="card">...</div>'
        },
        topbar: {
            undoredo: true
        }
    });
</script>
```
