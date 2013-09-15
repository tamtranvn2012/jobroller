<?php

/**
 * Utility Class for Creating Tables
 */
abstract class APP_Table{

	protected function table( $items, $attributes = array() ){

		$table_body = '';

		$table_body .= html( 'thead', array(), $this->header( $items ) );
		$table_body .= html( 'tbody', array(), $this->rows( $items ) );
		$table_body .= html( 'tfoot', array(), $this->footer( $items ) );

		return html( 'table', $attributes, $table_body );

	}

	protected function header( $data ){}

	protected function footer( $data ){}

	protected function rows( array $items ){

		$table_body = '';
		foreach( $items as $item ){
			$table_body .= $this->row( $item );
		}

		return $table_body;

	}

	abstract protected function row( $item );

	protected function cells( $cells, $type = 'td' ){

		$output = '';
		foreach( $cells as $value ){
			$output .= html( $type, array(), $value );
		}
		return $output;

	}

}