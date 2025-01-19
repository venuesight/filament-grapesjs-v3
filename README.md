# Filament Grapesjs V3

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]][link-license]


![image](https://github.com/dotswan/filament-grapesjs-v3/assets/20874565/2ad36e55-4d56-42f6-8946-b894dab5d4fa)


## Introduction

This package extends Filament to include a field type called Grapesjs, leveraging the Grapesjs library to enable visual editing of HTML codes within the Filament components. It allows users to interactively design and incorporate HTML elements via drag-and-drop functionality.


* Features include:
    * Integration of the Grapesjs library into Filament components.
    * Drag-and-drop functionality for visually designing HTML elements.
    * Simplified HTML code editing within Filament.
* Latest versions of PHP and Filament
* Best practices applied:
    * [`README.md`][link-readme] (badges included)
    * [`LICENSE`][link-license]
    * [`composer.json`][link-composer-json]
    * [`.gitignore`][link-gitignore]
    * [`pint.json`][link-pint]

## Installation

You can easily install the package via Composer:

```bash
composer require dotswan/filament-grapesjs-v3
```

## Publish Configuration

```bash
php artisan vendor:publish --tag="filament-grapesjs-config"
```

## Basic Usage

Ensure that your model has a column for the Grapesjs field. For example, if you have a `page_layout` column in your model, and **make sure that it's a text or longtext column in your database**, you can add the following to your `$fillable` array:

```php
protected $fillable = [
    'page_layout',
];
```

Then, add the Grapesjs field to your resource file:

```php
<?php
namespace App\Filament\Resources;
use Filament\Resources\Resource;
use Filament\Resources\Forms\Form;
use Dotswan\FilamentGrapesjs\Fields\GrapesJs;
...

class FilamentResource extends Resource
{
    ...
    public static function form(Form $form)
    {
        return $form->schema([
                GrapesJs::make( 'page_layout' )
                    ->tools([
                        // tools you want to include
                    ])
                    ->plugins([
                        'grapesjs-tailwind',
                        // other plugins you've included in your resources directory and referenced in filament-grapesjs.php
                        // e.g. 'gjs-blocks-basic, https://github.com/GrapesJS/blocks-basic'
                    ])
                    ->settings([
                        'pluginsOpts' => [
                            'grapesjs-custom-code' => [
                                'firstOption' => 'here',
                            ],
                        ],
                        'storageManager' => [
                            'type' => 'local',
                            'options' => [
                                'local' => [
                                    'key' => 'gsproject-test',
                                ],
                            ],
                        ],
                        'styleManager' => [
                            'sectors' => [
                                [
                                    'name' => 'General',
                                    'open' => false,
                                    'buildProps' => [
                                        'background-color',
                                        // other properties you want to include
                                    ],
                                ],
                            ],
                        ],
                        // Need to load js or css into the editor so it cares about your exteral assets?
                        'canvas' => [
                            'styles' => [
                                'https://yoursite.com/css/styles.css',
                            ],
                            'scripts' => [
                                'https://cdn.tailwindcss.com/3.4.16',
                              ]
                        ],
                    ])
                    ->id( 'page_layout' )
           ]);
    }
    ...
}
```

## Adding Plugins

To add a plugin, you need to include the plugin in your resources directory and reference it in the `filament-grapesjs.php` config file.

### Critical notes...
1) **The plugin must be in your resources/[whatever] directory**
2) **After adding assets to the resources directory and configuring, you must run `composer dump-autoload`**.  If you do not, your plugin and config will not be written into the `public` directory and it will fail!

For example, if you have a plugin called `gjs-blocks-basic` in your resources directory, you would add the compiled plugin files to your application, then add following to the `assets` array in the config file:

```php
return [

    'assets' => [

        'css' => [
            // slug => path to js file in your resources directory
           // 'slug' => 'path/to/js/file.js',
        ],

        'js' => [
            // slug => path to css file in your resources directory
            // 'slug' => 'path/to/css/file.css',
            'gjs-blocks-basic' => 'js/grapesjs-plugins/gjs-blocks-basic/dist/index.js',
        ]
    ]
];
```
Then, add the plugin to the plugins array in the `GrapesJs` field in your resource file using the plugin name defined by the package. For example:

```php
GrapesJs::make( 'page_layout' )
    ->tools([
        // tools you want to include
    ])
    ->plugins([
        'gjs-blocks-basic',
    ])
    ->settings([
        'pluginsOpts' => [
            // Optional settings for the plugin
            'gjs-blocks-basic' => [
                'firstOption' => 'here',
            ],
        ],
        // Rest of your config here...
    ])
    ->id( 'page_layout' )
```


```php
'gjs-blocks-basic' => 'grapesjs-plugins/gjs-blocks-basic',
```

## Output to an HTML page
So, GrapesJS is amazing, *but*... It does not store your styles in the editor HTML, instead it's stored in a delightfully nightmarish json object.  This is required by the editor so when your content is repopulated, it knows what to do with it, but it makes it pretty terrible to render the data, and a never ending list of stackoverflow posts didn't help me, so...

1) The data is stored in the database column as a massive block of json with the following attributes:
    - projectData: This is what the editor uses.  Don't mess with it. Your styles and blocks will go bye bye
    - style: A nice compact block of CSS, i.e. `* { box-sizing: border-box; } body {margin: 0;}`
    - html: This is the sweet stuff.  It's all the HTML from the editor (minus the `<body></body>` tags)

### Soooo, how do I add it to an HTML page?
Easy enough!  For example, to get the data
```php
<?php
use Illuminate\Support\Str;

$data = \App\YourModel::find( 1 );
$content = Str::isJson( $data->content )
    ? json_decode( $data->content, true )
    : [];
```

In your blade file or wherever you're using it...
```php
<style>
{!! data_get( $content, 'style' ) !!}
</style>
<body>
{!! data_get( $content, 'html' ) !!}
</body>
```

## License

[MIT License](LICENSE.md) © Dotswan

## Security

We take security seriously. If you discover any bugs or security issues, please help us maintain a secure project by reporting them through our [`GitHub issue tracker`][link-github-issue]. You can also contact us directly at [tech@dotswan.com](mailto:tech@dotswan.com).

## Contribution

We welcome contributions! contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


[ico-version]: https://img.shields.io/packagist/v/dotswan/filament-grapesjs-v3.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dotswan/filament-grapesjs-v3.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dotswan/filament-grapesjs-v3
[link-license]: https://github.com/dotswan/filament-grapesjs-v3/blob/master/LICENSE.md
[link-downloads]: https://packagist.org/packages/dotswan/filament-grapesjs-v3
[link-readme]: https://github.com/dotswan/filament-grapesjs-v3/blob/master/README.md
[link-github-issue]: https://github.com/dotswan/filament-grapesjs-v3/issues
[link-composer-json]: https://github.com/dotswan/filament-grapesjs-v3/blob/master/composer.json
[link-gitignore]: https://github.com/dotswan/filament-grapesjs-v3/blob/master/.gitignore
[link-pint]: https://github.com/dotswan/filament-grapesjs-v3/blob/master/pint.json
[link-author]: https://github.com/dotswan
