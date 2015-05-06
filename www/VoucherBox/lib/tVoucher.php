<?php
/**
 *
 * @class tVoucher
 * @author Luigi Capriotti, on the basis of the UniqueCodeGenerator class by Darren Inwood, Chrometoaster New Media Ltd
 *
 */ 
class tVoucher {
    /** Holds the SQLite database */
    private $db;

    /** Holds the options */
    private $options;

    /**
     * Constructor.
     */
    public function __construct($options=null) {
        // Set up options
        if ( ! is_array($options) ) {
            $options = array();
        }
        $defaults = $this->get_default_options();
        $this->options = array_merge( $defaults, $options );
        // Connect to db
        $this->connect();
    }

    /**
     * Returns the default options for the class. Override any of these in the
     * $options parameter of the retrieveVoucher() function.
     * @return (Array) Associative array of default options.
     */
    private function get_default_options() {
        return array(
            'db_host' => 'localhost',
            'db_name' => 'radius',
            'db_user' => 'username',
            'db_pass' => 'password',
            'db_table' => 'radcheck',
            'charset' => '1234679acdefghjkmnpqrtuvwxy',
        );
    }

    /**
     * Connects to the database, stores the handle as $this->db.
     */
    private function connect() {
        // Connect
        $this->db = mysql_connect(
            $this->options['db_host'],
            $this->options['db_user'],
            $this->options['db_pass']
        );

	mysql_select_db( $this->options['db_name'] );
    }

    /**
     * Generates unique codes
     * @param $count (Integer) Number of codes to generate.
     * @param $validity (Integer) voucher validity in days
     * @param $length (Integer) Character length of each code.
     */
    public function generate($count, $validity, $length) {
        // Can we generate this many keys?
        $sane = $this->sanity_check($count, $length);
        if ( ! $sane ) {
            return;
        }
        $existing = $this->count();
        $writes = 0;
        // Generate... use while loop so we keep going till we have enough codes
        while ( $this->count() < $existing + $count ) {
            $new_code = $this->generate_code($length);
            $sql = sprintf(
                "INSERT INTO %s SET username='%s',attribute='Expiration',op='==',value='D%s'",
                $this->options['db_table'],
                $new_code,
		$validity
            );
            $result = mysql_query($sql);
			if ( $result === FALSE ) {
				echo "Could not write to database, exiting...\n\n";
            	die(mysql_error()); // TODO: better error handling
			}
			$writes++;
			if ( $writes % 100 == 0 ) {
				echo "$writes codes...\n";
            }
        }
        echo "\n$writes codes were generated.\n";
		echo "$count new vouchers generated and stored in the specified database.\n\n";
        return;
    }

    /**
     * Retrieves voucher from the database
     * @param $voucherCode (String) The first available voucher string
     * @param $validity (Integer) voucher validity in days
     * @param $voucherPrefix (String) String to add to the selected voucher code
     */
    public function retrieve($validity, $voucherPrefix) {
		$sql = sprintf("SELECT username FROM %s WHERE (op='==' AND value='D%s')", $this->options['db_table'], $validity);
		$result = mysql_query($sql);
		if ( $result === FALSE ) {
			return "voucherError";
		}

		$i = mysql_fetch_array($result);
		$voucherCode = $i['username'];

		/* UPDATE radcheck SET op=':=',username='testv9gjxye1' WHERE (op='==' AND username='v9gjxye1') */
		$sql = sprintf("UPDATE %s SET op=':=',username='%s' WHERE (op='==' AND username='%s')", $this->options['db_table'], "$voucherPrefix$voucherCode", $voucherCode);
		$result = mysql_query($sql);
		if ( $result === FALSE ) {
			return "voucherError";
		}

		return "$voucherPrefix$voucherCode";
    }

    /**
     * Generates a random code of a given length.  Uses only characters in the
     * charset, set in the $options array.
     * @param $length (Integer) Number of characters to put in the code.
     * @return (String) Randomly generated string.
     */
    private function generate_code($length) {
        $random= "";
        $data = $this->options['charset'];
        for($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand()%(strlen($data))), 1);
        }
        return $random;
    }

    /**
     * Does a sanity check, prints out some info to the console/screen, and returns
     * whether the request is sane or not.
     */
    private function sanity_check($count, $length) {
        // Outputs:
        // Possible generated codes: xxx
        // Possible secure codes: xxx
        // Existing codes: xxx
        // Can [not ]generate xxx new codes.
        $possible_total = pow( strlen($this->options['charset']), $length );
        $a = log( $possible_total, 2 );
        $possible_secure = pow( 2, 0.5 * $a );
        echo 'Possible generated codes: '.$possible_total."\n";
        echo 'Possible secure codes: '.$possible_secure."\n";
        $current_codes = $this->count();
        echo 'Existing codes: '.$current_codes."\n";
        $possible = true;
        if ( $possible_secure - $current_codes < $count ) {
            $possible = false;
        }
        echo 'Can '.($possible ? '' : 'not ').'generate '.$count.' new codes.'."\n";
        return $possible;
    }

    /**
     * Returns the number of codes currently in the database.
     * @return (Integer) The current number of codes in the database.
     */
    public function count() {
        $sql = sprintf("SELECT COUNT(username) AS count FROM %s", $this->options['db_table']);
		$result = mysql_query($sql);
		if ( $result === FALSE ) {
			die(mysql_error()); // TODO: better error handling
		}
        $current_codes = mysql_fetch_array( $result );
        return (int)$current_codes['count'];
    }

    /**
     * Returns the number of codes being used currently in the database.
     * @return (Integer) The current number of codes being in the database.
     */
    public function countInUse() {
        $sql = sprintf("SELECT COUNT(username) AS count FROM %s WHERE LENGTH(value)>5", $this->options['db_table']);
		$result = mysql_query($sql);
		if ( $result === FALSE ) {
			die(mysql_error()); // TODO: better error handling
		}
        $current_codes = mysql_fetch_array( $result );
        return (int)$current_codes['count'];
    }

    /**
     * Returns the number of available vouchers currently in the database (those with op='==')
     * @param $validity (Integer) Type of voucher to check
     * @return (Integer) The current number of codes in the database.
     */
    public function countAvailable($validity) {
        $sql = sprintf("SELECT COUNT(username) AS count FROM %s WHERE op='==' AND value='D%s'", $this->options['db_table'], $validity);
		$result = mysql_query($sql);
		if ( $result === FALSE ) {
			die(mysql_error()); // TODO: better error handling
		}
        $current_codes = mysql_fetch_array( $result );
        return (int)$current_codes['count'];
    }

    /**
     * Delete records with expired vouchers
     */
    public function purgeExpired() {
		$sql = sprintf("DELETE FROM %s WHERE value IN ( SELECT value FROM ( SELECT value FROM %s WHERE LENGTH(value)>5 ) x WHERE STR_TO_DATE(value, %s) < CURDATE() )", $this->options['db_table'], $this->options['db_table'], "'%d %b %Y  %H:%i:%s'");
		$result = mysql_query($sql);
		if ( $result === FALSE ) {
			die(mysql_error()); // TODO: better error handling
		}
    }
}

/* 


*/
?>

