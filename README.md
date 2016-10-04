# Forms [![Build Status](https://travis-ci.org/frozzare/wp-forms.svg?branch=master)](https://travis-ci.org/frozzare/wp-forms)

> Requires PHP 5.6 and WordPress 4.3

Create forms in using code in WordPress.

## Installation

```
composer require frozzare/wp-forms
```

## Example

```php
// Add custom field.
forms()
    ->add_field( 'custom', function ( $attributes ) {
        return sprintf( '<p><input type="text" name="%s" /></p>', $attributes['name'] );
    } );

// Add form.
forms()
    ->add( 'contact', [
        'name'  => [
            'label' => 'Name',
            'rules' => 'required|max:250'
        ],
        'email'  => [
            'label' => 'Email',
            'type'  => 'email',
            'rules' => 'required|email'
        ],
        'custom' => [
            'label' => 'Custom',
            'type'  => 'custom'
        ],
        'text'   => [
            'label' => 'Text',
            'type'  => 'textarea'
        ],
        'color'  => [
            'label' => 'Select color',
            'type'  => 'select',
            'items' => [
                [
                    'text'  => 'Blue',
                    'value' => 'blue',
                ],
                [
                    'text'  => 'Green',
                    'value' => 'green'
                ]
            ]
        ]
    ] )
    ->button( 'Send' )
    ->save( function ( array $data ) {
        // Do something with the data...

        // Return true if you will save or email the data yourself
        // otherwise false to save in forms data post type.
        return false;
    } );

// Get errors.
$errors = forms()->errors( 'contact ');

// Render form.
forms()
    ->render( 'contact' );

```

## Save form with ajax

Example

```js
$('.form-submit').on('click', function (e) {
    e.preventDefault();

    $.post('/forms-ajax/?action=save&form=contact', $('form').serialize(), function (res) {
        // res.success is true or false
        // res.errors contains all errors if any.
    });
});
```

## Validation rules

```
alpha, alpha_num, array, between, bool, digit, email, float, int,
ip, min, max, numeric, required, size, string, url
```

## Contributing

Everyone is welcome to contribute with patches, bug-fixes and new features.

## License

MIT © [Fredrik Forsmo](https://github.com/frozzare)
