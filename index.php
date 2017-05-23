<?php
$pathPrefix = dirname($_SERVER['SCRIPT_NAME']);
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.1/angular.js"></script>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <script type="text/javascript" src="<?php print $pathPrefix; ?>/app.js"></script>
    <script type="text/javascript">
    	angular.module('uxprototypeApp').value("pathPrefix", "<?php print $pathPrefix; ?>");
    </script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.10.0/ui-bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php print $pathPrefix; ?>/theme.css"> 

	<title ng-bind="title"><?php echo $page_title ?></title>
	  <meta name="description" ng-attr-content="{{description}}" content="<?php echo $meta_description ?>">

</head>

<body ng-app="uxprototypeApp">
    <div ng-controller="SearchCtrl">
    	<div ng-show="!products.length">
    		Loading products...
    	</div>
    	<div ng-show="products.length">
        <section class="container">
            <h2>Search results for "{{searchTerm}}" ({{ (filteredProducts| filter: searchTerm).length}})</h2>
            <p>Search:
                <input id="search-input" type="text" placeholder="Search" name="q" data-ng-model="searchTerm">
            </p>
        </section>
        <div class="container">
            <section class="row controls-sorting">
                <div class="sorting col-sm-6"> <span class="title">Sort by:</span>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs" data-ng-click="setOrder('2', 'Brand', false)" data-ng-class="{active: sorting.id  === '2'}">Name A-Z</button>
                        <button type="button" class="btn btn-default btn-xs" data-ng-click="setOrder('3', 'Brand', true)" data-ng-class="{active: sorting.id  === '3'}">Name Z-A</button>
                        <button type="button" class="btn btn-default btn-xs" data-ng-click="setOrder('4', 'Price', false)" data-ng-class="{active: sorting.id  === '4'}">Price Low-High</button>
                        <button type="button" class="btn btn-default btn-xs" data-ng-click="setOrder('5', 'Price', true)" data-ng-class="{active: sorting.id  === '5'}">Price High-Low</button>
                    </div>
                </div>
            </section>
            <div class="row">
                <aside class="searchfilters col-sm-3">
                    <div class="facets">
                        <section class="facetgroup" ng-if="filteredProducts.length > 0">
                            <h4>Brands</h4>
                            <!-- works but haven't found a way to abstract it to make it scalable for multiple filter groups  -->
                            <div data-ng-repeat="brand in brandsGroup | limitTo: maxBrands" data-ng-if="(filteredProducts | filter:searchTerm | filter:count('Brand', brand)).length > 0">
                                <label class="checkbox">
                                    <input type="checkbox" data-ng-model="useBrands[brand]" /> {{brand}} <span>({{ (filteredProducts | filter:searchTerm | filter:count('Brand', brand)).length }})</span> </label>
                            </div>
                            <span data-ng-show="maxBrands<6" class="link" data-ng-click="maxBrands = 100">More</span> <span data-ng-show="maxBrands>5" class="link" data-ng-click="maxBrands = 5">Less</span>
                        </section>
                        <section class="facetgroup" ng-if="filteredProducts.length > 0">
                            <h4>Labels</h4>
                            <div>
                                <!--- Not working correctly, ideally this should be iterated rather than hardcoded and the count doesn't match if there are multiple values in the array -->
                                <label class="checkbox">
                                    <input type="checkbox" data-ng-model="filters.Label['New Product']" /> New Product <span>({{ (filteredProducts | filter:searchTerm | filter:count('Labels', 'New Product')).length }})</span> </label>
                                <label class="checkbox">
                                    <input type="checkbox" data-ng-model="filters.Label['As Advertised']" /> As Advertised <span>({{ (filteredProducts | filter:searchTerm | filter:count('Labels', 'As Advertised')).length }})</span> </label>
                                <label class="checkbox">
                                    <input type="checkbox" data-ng-model="filters.Label['Bargain Buy']" /> Bargain Buy <span>({{ (filteredProducts | filter:searchTerm | filter:count('Labels', 'Bargain Buy')).length }})</span> </label>
                            </div>
                        </section>
                        <section class="facetgroup" ng-if="filteredProducts.length > 0">
                            <h4>Callouts</h4>
                            <div ng-controller="ProductCalloutsCtrlr">
                                <!--- Not working correctly -->
                                <label class="checkbox" ng-repeat="callout in callouts.types">
                                    <input type="checkbox" data-ng-model="callout.checked" ng-change="updateCallouts()"/> {{callout.name}} <span> ({{ (filteredProducts | filter:searchTerm | filter:count('Labels', 'Sale')).length }})</span>
                                </label>
                            </div>
                        </section>

                    </div>
                </aside>
                <div class="productlist grid col-sm-9 clearfix">
                    <article data-ng-repeat="product in filteredProducts | inProductCallouts | filter:searchTerm | orderBy: sorting.order:sorting.direction" id="{{product.Sku}}" class="product col-sm-4">
                        <b class="decor" data-ng-hide="!product.Labels[0]">{{product.Labels[0]}}</b>
                        <b class="decor" data-ng-show="!product.Labels[0] && product.Callout[0]">{{product.Callout[0]}}</b>
                        <div class="inner-content">
                            <a class="thumb" data-sku="{{product.Sku}}" data-ng-href="#/product/{{product.Sku}}"> <img src="http://placehold.it/180x180"> </a>
                            <div class="clearfix">
                                <h4 class="productname" name="{{product.Heading}}"><a data-ng-href="#/product/{{product.Sku}}">{{product.Brand}} {{product.Heading}}</a></h4>
                                <div class="price" price="{{product.Price}}"><sup>$</sup><span>{{product.Price}}</span>
                                    <div ng-if="product.WasPrice > 0 && product.WasPrice > product.Price" class="wasprice">was $<span>{{product.WasPrice}}</span> </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

           

            <script>
                // tell the embed parent frame the height of the content
                if (window.parent && window.parent.parent) {
                    window.parent.parent.postMessage(["resultsFrame", {
                        height: document.body.getBoundingClientRect().height,
                        slug: "None"
                    }], "*")
                }
            </script>
        </div>
      </div>
		</div>
</body>

</html>