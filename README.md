# WordPress Customizable Options

Library that allows to edit simple text options in preview part of WordPress Customizer, on frontend.

## Installation

#### Composer

```
$ composer require viktor777/wp-customizable-options
```

## Usage

#### Initialization

```
register_customizable_options();
```

#### Editable option

```
the_customizable_text( $option, $default = false );
```

#### Change default wrapper

By default text is wrapped in tag 'span' when admin is in Customizer. You can change it with filter:

```
add_filter( 'the_customizable_text_wrapper_tag', function () {
    return 'div';
} );
```

#### Change default filter

There is 'esc_html' function is implemented to text by default. You can change it with next code:

```
/**
 * Remove default filter
 */
remove_filter( 'the_customizable_text_value', '_the_customizable_text_value' );
/**
 * Lets use e.g. 'esc_url' as filter
 */
add_filter( 'the_customizable_text_value', function () {
    return is_bool( $value ) ? $value : esc_url( $value );
} );
```

## Demo

https://youtu.be/pCT5_stDPYM

## Notes

* If you do not add setting in Customizer with your code, it will be stored in section which is called 'Customizable Options' by default
* Label of control in Customizer will be generated from option name by default, e.g. 'customizable_option' => 'Customizable Option'
