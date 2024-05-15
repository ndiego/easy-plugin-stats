<?php
/**
 * Utility functions.
 *
 * @package EasyPluginStats
 */

namespace EasyPluginStats;

/**
 * Formats a number based on provided or default settings.
 *
 * @since 2.0.0
 *
 * @param float|int $data The number to format.
 * @return string         The formatted number.
 */
function format_numbers( $data ) {
    // Apply filters to allow customization of number formatting.
    $number_format = apply_filters( 'eps_number_format', array(
        'decimals'      => 0,
        'dec_point'     => '.',
        'thousands_sep' => ','
    ) );

    // Format the number based on the specified formatting options.
    return number_format(
        $data,
        $number_format['decimals'],
        $number_format['dec_point'],
        $number_format['thousands_sep']
    );
}