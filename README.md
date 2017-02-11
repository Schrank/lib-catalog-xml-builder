# Catalog XML Builder

This library helps to build a catalog XML file in Lizards & Pumpkins format


## Getting started

### Product Data

    $productData = [
        'type_id'    => 'simple',
        'sku'        => '123',
        'tax_class'  => 7,
        'attributes' => [
            'visibility' => 3,
            'url_key' => 'lalala-cool-seo-url',
            'non_canonical_url_key' => [
                'foo/bar.html',
                'foo/buz.html',
                'qux/foo.html',
            ],                
        ],
        'images' => [
            [
                'main'  => true,
                'file'  => 'some/file/somewhere.png',
                'label' => 'This is the label',
            ],
        ],
        'associated_products' => [
            [
                'sku'        => 'associated-product-1',
                'tax_class'  => 4,
                'attributes' => [
                    'stock_qty' => 12,
                    'visible'   => true,
                    'color'     => 'green',
                ],
            ],
        ],
        'variations' => [
            'size',
            'color',
        ],
    ];

### Catalog Data
