<?php

namespace PGMB\Mustache;

/**
 * Interface for creating a custom Mustache context
 */
interface MustacheContextProviderInterface {
	/**
	 * The root key for the Mustache context
	 *
	 * {{the_root_key.}}
	 *
	 * @return string
	 */
	public function get_key(): string;

	/**
	 * An array containing the individual data values
	 *
	 * {{the_root_key.build_array_foo}}
	 * {{the_root_key.build_array_bar}}
	 *
	 * @return array
	 */
	public function build(): array;


	/**
	 * An array describing the values available within the context, for use in the variable inspector
	 *
	 *
	 * @return array
	 * [
	 *      'build_array_foo' => [
	 *          'type'  => 'string', //html, date, url, number, boolean, object, list
	 *          'label' => 'Label for foo', //optional
	 *          'example' => 'Example value', //optional
	 *      ],
	 *      'build_array_bar' => [
	 *          'type' =>  'object',
	 *          'children' => [
	 *              'foo'   => [
	 *                  'type' => 'number',
	 *                  'label' => 'Foo',
	 *              ],
	 *              'bar'   => [
	 *                  'type' => 'string',
	 *                  'label' => 'Bar',
	 *              ],
	 *          ]
	 *      ]
	 * ]
	 */
	public function describe(): array;
}