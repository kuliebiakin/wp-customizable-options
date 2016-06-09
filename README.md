# WordPress Customizer Preview Live Edit

Library that allows to edit simple text options in preview part of customizer, on frontend.

## Installation

#### Composer

```
$ composer require viktor777/customize-preview-edit
```

## Usage

#### Initialization

```
new Customize_Preview_Edit\Initialization( get_template_directory_uri() . '/path/to/lib/dir' );
```

#### Editable option

```
the_customizable_text( $option, $default = null, $tag = 'span' );
```

## Notes

* Options are wrapped by default in tag 'span' when admin is in Customoizer
* You need to set 'postMessage' as 'transport' and 'option' as 'type' in [setting options](https://developer.wordpress.org/themes/advanced-topics/customizer-api/#settings)