# DataObject Link Extension
Give DataObjects an own url and a page to display them on.

## Configuration
#### 1. Extension
Add the ``DataObjectLinkExtension`` to your DataObject and the ``DataObjectLinkExtension_Controller`` to your page controller where you want the item to be displayed.

```yaml
Item:
  extensions:
    - DataObjectLinkExtension
ItemCategoryPage_Controller:
  extensions:
    - DataObjectLinkExtension_Controller
```

#### 2. config.yml

```yaml
DataObjectLinkMapping:
  produkt: 
  	class: 'Item'
  	id_instead_of_slug: false
  	template: 'CoolItemPage'
```

You need to create a mapping for each URL Action. This action needs to be unique. So in this example, you can't create another one called "produkt"
For each action you must define a class (DataObject classname), if you want to use the ID as url segment instead of the slug and you are able to submit a specific template.
By default a template called "ClassNamePage" would be used. In this case "ItemPage"

**The URL Action must be unique!**

#### 3. DataObject

```php
  public function getHolderPage() {
    return $this->Page();
  }
```

If your DataObject is linked to a specific page, you could use this function to provide that page. It will be used in the Link() function. Otherwise the link would be the current page.

