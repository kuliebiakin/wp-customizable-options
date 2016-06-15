# WordPress Customizable Options

Library that allows to edit simple text options in preview part of WordPress Customizer, on frontend.

## Installation

#### Composer

```
$ composer require viktor777/customize-preview-edit
```

## Usage

#### Initialization

```
new WPCustomizeOptions\Initialization();
```

#### Editable option

```
the_customizable_text( $option, $default = null );
```

## Notes

* Options are wrapped by default in tag 'span' when admin is in Customizer
* You need to set 'postMessage' as 'transport' and 'option' as 'type' in [setting options](https://developer.wordpress.org/themes/advanced-topics/customizer-api/#settings)