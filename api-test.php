<?php



	class query_set {
		
		var $table_name,
			$column_names,
			$json_keys,
			$url_filter,
			$url_value,
			$results,
			$poolFilter;
			
		public function __construct( $table_name ){
			$this->table_name = $table_name;
			$this->poolFilter = array();
		}
		
		function json_keys_column_names($x){
			$this->json_keys_column_names = $x;
		}
		
		function url_filter($filter, $value, $trueOrFalse){
			if(!empty($filter)){
				$this->url_filter = $filter;
			}else{
				$this->url_filter = "";
			}
			if(!empty($value)){
				$this->url_value = preg_replace("/[^A-Za-z0-9 ]/", '', $value);
			}else{
				$this->url_value = "";
			}
			if(!empty($trueOrFalse)){
				$this->trueOrFalse = $trueOrFalse;
				if(empty($this->url_value)){
					array_push($this->poolFilter, $this->url_filter . " IS NOT NULL"  );
				}
			}else{
				if(!empty($this->url_value)){
					array_push($this->poolFilter, $this->url_filter . " = " . '\'' .  $this->url_value . '\''  );
				}
			}

		}
		
		
		public function query_data(){
			$this->column_names_array = array();
			$this->query_build = '';
			$this->query_where = array();
			$this->query_columns = array();
			$this->tables = array();
			$this->join_table = array();
			$this->table_name_length = strlen($this->table_name);
			
			// loop through key value pairs
			foreach($this->json_keys_column_names as $key => $value){
				//if(strpos( $value, "_ID" ) !== false && strpos( $value, "_ID_" ) == false ){
					//echo $value . "    ";
				//}
				if(is_array($value)){
					foreach ($value as $value2) {
							if(is_array($value2)){

							}else{
								$this->build($value2);
							}
					}
				}else{
					$this->build($value);
				}
				
				
			}	


			$this->poolFilter = implode(" AND ", $this->poolFilter);
			$this->tables = implode(", ", $this->tables);
			$this->join_table = implode(" LEFT JOIN ", $this->join_table);
			$this->query_columns = implode(", ", $this->query_columns);
			$this->query_where = implode( " AND ", $this->query_where);
			$this->column_names_string = implode(", ", $this->column_names_array);
			
			if(empty($this->join_table)){
				if(!empty($this->poolFilter)){
					$this->query_build .= "SELECT " . $this->query_columns  . " FROM " . $this->table_name . " " . $this->table_name .   " WHERE " . $this->table_name . ".ID = "  . $this->table_name . ".ID" . " AND " . $this->poolFilter;
				}else{
					$this->query_build .= "SELECT " . $this->query_columns  . " FROM " . $this->table_name . " " . $this->table_name .   " WHERE " . $this->table_name . ".ID = "  . $this->table_name . ".ID";
				}
			}else{
				if(!empty($this->poolFilter)){
					$this->query_build .= "SELECT " . $this->query_columns  . " FROM " . $this->table_name . " " . $this->table_name .   " LEFT JOIN " . $this->join_table . " WHERE " . $this->poolFilter;
				}else{
					$this->query_build .= "SELECT " . $this->query_columns  . " FROM " . $this->table_name . " " . $this->table_name .   " LEFT JOIN " . $this->join_table;
				}
			}
			

			return $this->query_build;
			
		}

		public function build($value){
			if ( $value !== "" ){
				// get the value from the table name from the column(value) and append it to a key value array where key is the origional key.
				if( strpos( $value, "_ID" ) !== false && strpos( $value, "_ID_" ) == false && strpos( $value, $this->table_name ) === false ){

					$before_id = str_replace("_ID", '', $value);
					$where_push = $this->table_name . "." . $value . " = " . str_replace("_ID", '', $value) . ".ID";
					$id_compare = $this->table_name . "." . $value  . " = "  . $before_id . ".ID";
					$table_on_statement = $before_id . " " . $before_id . " ON " . $where_push;

					//COLUMNS
					array_push( $this->query_columns, str_replace("_ID", '', $value) . "." . "ID" . " as " . str_replace("_ID", '', $value) . "_ID" );

					//TABLES
					if( !in_array($table_on_statement, $this->join_table) ){
						array_push( $this->join_table, $table_on_statement);
					}
					

				}else if( strpos( $value, "_ID_" ) !== false ){
					
					//if the value contains the id, that means it's referencing another table.
					$before_id = substr($value, 0, strpos($value, '_ID_'));
					$fk_not_id = $before_id . "." . substr($value, strpos($value, "_ID_") + 4);
					$fk = substr($value, 0, strpos($value, '_ID_')) . "_" . "ID";
					$fk_not_id_underscore = substr($value, 0, strpos($value, '_ID_')) . "_" . substr($value, strpos($value, "_ID_") + 4);
					$where_push = $this->table_name . "." . $fk_not_id_underscore  . " = "  . $fk_not_id;
					$id_compare = $this->table_name . "." . $fk  . " = "  . $before_id . ".ID";
					$table_on_statement = $before_id . " " . $before_id . " ON " . $id_compare;
					
					//COLUMNS
					array_push( $this->query_columns, $fk_not_id . " as " . $value );
					
					//TABLES
					if( !in_array($table_on_statement, $this->join_table) ){
						array_push( $this->join_table, $table_on_statement);
					}
					
					
				}else{

					$after_table_name = substr($value, strpos($value, $this->table_name . "_") + ($this->table_name_length +1));
					$after_table_name_no_US = substr($value, strpos($value, $this->table_name . "_") + 0);
					$where_push = $this->table_name . " " . $this->table_name . " ON " . $this->table_name . ".ID"  . " = " . $this->table_name . "." . "ID";

					//COLUMNS
					if( !in_array($this->table_name . "." . $after_table_name . " as " . $value, $this->query_columns) ){
						array_push( $this->query_columns, $this->table_name . "." . $after_table_name . " as " . $value);
					}

					//TABLES
					// if( !in_array($where_push, $this->join_table) ){
					// 	array_push( $this->join_table, $where_push);
					// }

				}
			}
		}

		public function query_this($dbh, $query){
			
			$get_results;

			#$get_results = $dbh->prepare( $query );
			#$get_results->execute() or die(print_r($db->errorInfo(), true));
			#$this->results = $get_results->fetchAll();
            
            $dbh->query($query);
            $this->results = $dbh->to_assoc();
			$this->querying();

		}

		public function querying(){

			$output_inner_array = array();

			foreach ($this->results as $row) {

				$output_inner = "";
				$output_data = array();
				$multiple_value = array();
				$output_inner .= "{";

				foreach($this->json_keys_column_names as $key => $value){


					if(is_array($value)){
						foreach ($value as $value2) {
							if(is_array($value2)){
								foreach($value2 as $value3){
									array_push($multiple_value, $value3);	
								}
							} else {
    						    array_push($multiple_value, $row[$value2]);
							}
							
						}
						//$output_data .= "\"" . implode(" ", $multiple_value) . "\"";
						array_push($output_data, "\"" . $key . "\": ". "\"" . implode("", $multiple_value) . "\"");
					}else{
						//$output_data .= "\"" . $key . "\": ". "\"" . $row[$value] . "\", ";
						array_push($output_data, "\"" . $key . "\": ". "\"" . $row[$value] . "\"");
					}

				}

				$output_data_result = implode(", ", $output_data);
				$output_inner .= $output_data_result;
				$output_inner .= "}";
				array_push( $output_inner_array, $output_inner );
			}

			$output_inner = implode(", ", $output_inner_array);


			$output = "[".$output_inner."]";



			print_r($output);

			$results = NULL;

		}
		

		public function query_read_write(){

		}
		
	}
	

	
?>