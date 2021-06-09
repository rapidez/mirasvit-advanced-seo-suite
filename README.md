# Mirasvit Advanced SEO Suite

## Requirements

You need to have the [Mirasvit Advanced SEO Suite](https://mirasvit.com/magento-2-extensions/advanced-seo-suite.html) module installed and configured within your Magento 2 installation.

## Installation

```
composer require rapidez/mirasvit-advanced-seo-suite
```

### Redirects

To enable the redirects you've to add the middleware the `web` middleware group in your `app/Http/Kernel.php`
```
\Rapidez\MirasvitAdvancedSeoSuite\Http\Middleware\MirasvitSeoRedirects::class,
```
*Note: Wildcards within redirects are currenlty not supported!*

### Templates

Just use `Template::content($model, $field)` as fallback. The first parameter is the model and the second the attribute. For example on the product page in `resources/views/vendor/rapidez/product/overview.blade.php` ([make sure you've published the views](https://docs.rapidez.io/0.x/theming.html#views)):
```
@section('title', $product->meta_title ?: Rapidez\MirasvitAdvancedSeoSuite\Models\Template::content($product, 'meta_title'))
@section('description', $product->meta_description ?: Rapidez\MirasvitAdvancedSeoSuite\Models\Template::content($product, 'meta_description'))
```
Or create an [accessor](https://laravel.com/docs/master/eloquent-mutators#defining-an-accessor) on the models for the meta title and description.

## Autolinks

Use `Rapidez\MirasvitAdvancedSeoSuite\Models\Autolink::replace($description)` where you want the autolinks for example on CMS pages, product- and category descriptions.

## Note

Currently only the redirects, templates and autolinks are partly implemented.

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
