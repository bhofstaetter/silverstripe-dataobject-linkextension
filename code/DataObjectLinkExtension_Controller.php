<?php
class DataObjectLinkExtension_Controller extends DataExtension {

	private $config;

  private static $allowed_actions = [
    'show'
  ];

  private static $url_handlers = [
    '//$Action!/$ID!' => 'show',
  ];

  public function getItem() {
    $r = $this->owner->request;

    $action = $r->allParams()['Action'];
    $url = $r->allParams()['ID'];

    if($action && $url) {
      $config = Config::inst()->get('DataObjectLinkMapping', $action);

      if($config) {
      	$this->config = $config;

      	if(!$config['id_instead_of_slug']) {
		      $searchField ='URLSegment';
	      } else {
		      $searchField ='ID';
	      }

        $item = $config['class']::get()->find($searchField, $url);
				if($item->canView()) {
					return $item;
				}
      }
    }
  }

  public function show() {
    $item = $this->getItem();

    if($item) {
      $parent = Director::get_current_page();

      $data = [
        'Title' => $item->Title,
        'Parent' => $parent,
        'ClassName' => $item->ClassName,
        'Item' => $item,
        'Breadcrumbs' => $this->DataobjectBreadcrumbs()
      ];

      $pageTemplate = false;
      if(isset($this->config['template']) && $this->config['template']) {
        $pageTemplate = $this->config['template'];
      }

      return $this->owner
        ->customise($data)
        ->renderWith([$pageTemplate, $item->ClassName . 'Page', 'Page']);
    }

    return $this->owner->httpError(404);
  }

  public function DataobjectBreadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
    $pages = $this->getDataobjectBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
    $template = new SSViewer('BreadcrumbsTemplate');
    return $template->process($this->owner->customise(ArrayData::create([
      "Pages" => $pages,
      "Unlinked" => $unlinked
    ])));
  }

  public function getDataobjectBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false) {
    $page = $this->getItem();
    $page->ShowInMenus = true;
    $page->MenuTitle = $page->Title;
    $page->Parent = $this->owner;
    $pages = [];

    while(
      $page
      && (!$maxDepth || count($pages) < $maxDepth)
      && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
    ) {
      if($showHidden || $page->ShowInMenus) {
        $pages[] = $page;
      }

      $page = $page->Parent;
    }

    return ArrayList::create(array_reverse($pages));
  }
}