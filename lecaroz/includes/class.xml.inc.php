<?php
//requ

class XMLClass {
	// Propiedades
	var $parser;
	var $name;
	var $attr;
	var $data  = array();
	var $stack = array();
	var $keys;
	var $path;
	
	var $source;
	var $type;
	var $encoding;
	
	// Constructor
	function XMLClass($source, $type, $encoding = 'ISO-8859-1') {
		if (trim($source) == '')
			$this->error('No hay origen o contenido de datos');
		else if (!in_array($type, array('file', 'contents')))
			$this->error('El tipo de contenido solo puede ser "file" o "contents"');
		else if (!in_array($encoding, array('ISO-8859-1', 'UTF-8', 'US-ASCII')))
			$this->error('Solo se permiten los tipos de codificaci&#243n "ISO-8859-1", "UTF-8" y "US-ASCII"');
		
		$this->type = $type;
		$this->source  = $source;
		$this->encoding = $encoding;
	}
	
	function autoParse() {
		$this->parser = xml_parser_create($this->encoding);
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		
		$data = implode("", file($this->source));
		
		xml_parse_into_struct($this->parser, $data, $this->data, $this->keys);
		xml_parser_free($this->parser);
	}
	
	// Analiza los datos XML
	function parse() {
		$data = '';
		$this->parser = xml_parser_create($this->encoding);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'startXML', 'endXML');
		xml_set_character_data_handler($this->parser, 'charXML');
		
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		
		if ($this->type == 'file') {
			// Abrir el archivo XML
			if (!($fp = @fopen($this->source, 'rb')))
				$this->error("No se pudo abrir '{$this->source}'");
			
			while (($data = /*fread($fp, 8192)*/fgets($fp)))
				if (!xml_parse($this->parser, $data, feof($fp)))
					$this->error(sprintf('XML error en la l&#237nea %d columna %d:<br>%s', xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser), xml_error_string(xml_get_error_code($this->parser))));
		}
		else if ($this->type == 'contents') {
			// Solo se pasa el contenido del documento XML
			$lines = explode("\n", $this->source);
			foreach ($lines as $val) {
				if (trim($val) == '')
					continue;
				
				$data = $val . "\n";
				if (!xml_parse($this->parser, $data))
					$this->error(sprintf('XML error en la l&#237nea %d columna %d:<br>%s', xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser), xml_error_string(xml_get_error_code($this->parser))));
			}
		}
		
		xml_parser_free($this->parser);
	}
	
	function startXML($parser, $name, $attr) {
		$this->stack[$name] = array();
		$keys = '';
		$total = count($this->stack) - 1;
		$i = 0;
		foreach ($this->stack as $key => $val) {
			if (count($this->stack) > 1) {
				if ($total == $i)
					$keys .= "['$key']";
				else
					$keys .= "['$key']";
			}
			else
				$keys .= "['$key']";
			$i++;
		}
		if (count($attr) > 0)
			eval('$this->data' . $keys . '/*[\'attr\']*/ = $attr;');
		$this->keys = $keys;
	}
	
	function endXML($parser, $name) {
		end($this->stack);
		if (key($this->stack) == $name)
			array_pop($this->stack);
	}
	
	function charXML($parser, $data) {
		if (trim($data) == '') return;
		
		eval('@$this->data' . $this->keys . '[\'data\'] .= trim(str_replace("\n", \'\', $data));');
	}
	
	function error($msg)    {
		echo "<div style=\"border: 1px solid #708090;background-color: #d3d3d3;padding: 5px 5px 5px 5px;\">";
		echo "<div style=\"font-weight:bold;color:#C00;padding-bottom:5px;\">Error:</div>";
		echo "<div style=\"font-weight:bold;\">$msg</div>";
		echo "</div>";
		
		xml_parser_free($this->parser);
		
		exit();
	}
}
?>