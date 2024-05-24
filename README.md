# Laravel Eloquent Localization and Translation

In today's globalized world, reaching audiences across borders is more important than ever. To effectively engage users from diverse backgrounds, providing your website's content in their native language is crucial. However, managing content translations can be a complex and time-consuming process.

This is where the Laravel Eloquent Localization and Translation package comes in. It offers a comprehensive and developer-friendly solution to streamline your content translation workflow.

## Challenges of Traditional Translation Management

-   **Manual Processes:** Manually translating content across different pages and models can be tedious and error-prone.
-   **Inconsistent Formatting:** Maintaining consistent formatting and style across translated content can be a significant challenge.
-   **Limited Scalability:** As your website grows and you add more languages, managing translations can become increasingly difficult.

## Features

The Laravel Eloquent Localization and Translation package empowers you to:

-   **Effortlessly Integrate Translations:** Integrate translation functionality into your Eloquent models with minimal code.
-   **Choose Your Storage Approach:** Select the most suitable method for your project - dedicated translations tables for a structured approach or storing translations within model attributes for simpler scenarios.
-   **Automatic Translation Loading:** Enjoy performance benefits with automatic eager loading of translations whenever models are retrieved.
-   **Easy Access to Translated Attributes:** Access translated content through convenient methods directly on your model instances.
-   **Artisan Command for Efficiency:** Leverage the artisan command to generate a starting point for your translations table migration, saving you development time.

By providing a flexible and developer-friendly solution, this package helps you efficiently manage content translations, ensuring a seamless experience for your global audience. This Laravel package empowers you to effortlessly manage translations for your Eloquent models. It offers two flexible approaches:

1. **Dedicated Translations Table:** This structured approach allows each model to have its own translations table, potentially including additional columns for specific needs.
2. **Model Attributes:** For simpler scenarios, translations can be stored directly as JSON within the model's attributes, providing a lightweight solution.

## Installation

1. Install the package using Composer:

    ```bash
    composer require msafadi/laravel-eloquent-l10n
    ```

2. Optionally, you can publish the configuration file for customization:

    ```bash
    php artisan vendor:publish --provider="Safadi\Eloquent\L10n\EloquentL10nServiceProvider"
    ```

## Configuration

The package configuration file (`config/eloquent-l10n.php`) provides options to tailor the package to your project's specific needs:

-   **Default Translation Model:** Specify the model class used for translations if using the dedicated translations table approach (can be left as the default provided by the package).
-   **Customization Options:**
    -   **Translations Table Postfix:** Override the default translations table name postfix (`_l10n`).
    -   **Locale Column:** Change the default column name for storing the locale identifier (`locale`).
    -   **Translation Model:** Customize the translation model (`Safadi\Eloquent\L10n\L10n`).

## Usage

### 1. Dedicated Translations Table

#### 1.1. Model Setup

-   Incorporate the `HasTranslationsModel` trait into your model class.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Safadi\Eloquent\L10n\Concerns\HasTranslationsModel;
use Safadi\Eloquent\L10n\Contracts\Translatable;

class Post extends Model implements Translatable
{
    use HasTranslationsModel;
}
```

#### 1.2. Usage

-   The package provides a dynamic `Translation` model that can be used for all translations tables.
-   Access translated attributes using the `attribute_name`, specifying the desired locale and attribute name:

```php
<?php

// Use the current application locale
$post = Post::find(1);
$translatedTitle = $post->title;

// Specify the locale manually
$post = Post::useLocale('ar')->find(1);
$translatedTitle = $post->title;

```

The package performs a left join with the related translations table constraited with the specified `locale` code.
This allow to access and call the translatable attributes from the parent model as if it was a single table.

#### 1.3. Create and Save Translations

The package provide convenit ways to save model translations, by performing upserts on the translations table.
If there's no existing translations for the specifed loacle it will be inserted otherwise it will updated.

```php
<?php
$post->setLocale('ar')->translate([
    'title' => 'Post Title Updated',
    'content' => 'Post content',
]);

// or
$post->translate([
    'title' => 'Post Title Updated',
    'content' => 'Post content',
], 'ar');

//
$post = Post::withTranslations([
    'en' => [
        'title' => 'Post Title',
        'content' => 'Post content',
        // ...
    ],
    'ar' => [
        'title' => 'عنوان المنشور',
        'content' => 'محتوى المنشور',
        // ...
    ],
    // ...
])->create([
    'status' => 'published',
    // ...
    // Non translatable post attributes
]);

```

#### 1.4. Translations Relationship

The package also define `HasNany` relationships named `translations`.

```php
<?php

echo Post::find(1)->translations()->count();

foreach (Post::find(1)->translations as $translation) {
    echo $translation->locale;
    echo $translation->title;
    // ...
}

```

You can also stop the default behaviour of the global scope which perform a join between the model table and its translations table, by calling `withoutTranslations`

```php
<?php

$posts = Post::withoutTranslations()->get();

echo Post::withoutTranslations()->count();

```

#### 1.5. Customization (Optional)

-   If you prefer to create a custom translations model class, you can define a relationship to your translations table using the `hasMany` method within your model.

#### 1.6. Artisan Command

Use the artisan command to generate a starting point for your translations table migration:

```bash
php artisan make:l10n-table MyModel
```

Replace MyModel with your actual model class name or the table name.

### 2. Model Attributes

#### 2.1. Model Setup

-   Include the `HasTranslatableAttributes` trait in your model class.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Safadi\Eloquent\L10n\Concerns\\HasTranslatableAttributes;
use Safadi\Eloquent\L10n\Contracts\Translatable;

class Post extends Model implements Translatable
{
    use HasTranslatableAttributes;

    public function translatableAttributes(): array
    {
        return ['title', 'content']; // Specify translatable attributes
    }
}
```

#### 2.2. Usage

-   The package store translations as JSON within the `translations` attribute, following the format:

```json
{
    "en": {
        "title": "English Title",
        "content": "English Content"
    },
    "fr": {
        "title": "Titre Français",
        "content": "Contenu Français"
    }
}
```

-   Access translated attributes using the `attribute` name as normal:

```php
$post = Post::find(1);

// Get title translated with the current application locale
$translatedTitle = $post->title;

// Get title translated with the specified locale
$translatedTitle = $post->setLocale('ar')->title;
```

#### 2.3. Save Translations

```php
$post = Post::create([
    'status' => 'published',
    'author' => 'Mohammed',
    // ...,
    'title' => [
        'en' => 'Post Title',
        'ar' => 'عنوان المنشور',
        // ...
    ],
    'content' => [
        'en' => 'Post content',
        'ar' => 'محتوى المنشور',
        // ...
    ],
]);

//
$post = new Post();
// Set all translations at once
$post->title = [
    'en' => 'Post Title',
    'ar' => 'عنوان المنشور',
    // ...
];
$post->save();

// Update partial translations
$post = Post::find(1);

// Update title in the current application locale, keep other translations without change.
$post->title = 'English';

// Specify the translation locale.
// This will update the `ar` translation and keep other translations without change.
$post->setLocale('ar')->content = 'عربي';
// ...
$post->save();

```

## Contributing

We value contributions to this package! Please refer to the contributing guide for details.

## License

This package is open-sourced under the MIT license.
