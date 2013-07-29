<?php

// LATEST VERSION: https://github.com/Churten/tt-rss-ff-xmllint

// to use this:
// create a directory ff_xmllint in <tt-rss>/plugins/
// copy this file to be <tt-rss>/plugins/ff_xmllint/init.php

class Ff_XmlLint extends Plugin {
	
	private $host;

	function about() {
		return array(1.1,
			"XmlLint",
			"cheetah@fastcat.org",
			false);
	}
	
	function api_version() {
		return 2;
	}

	function init($host) {
		$this->host = $host;
		$host->add_hook($host::HOOK_FEED_FETCHED, $this);
		$host->add_hook($host::HOOK_PREFS_TAB, $this);
	}
	
	function save() {
		$this->host->set($this, "tidy", checkbox_to_sql_bool($_POST["ff_xmllint_tidy"]));
		$this->host->set($this, "lint", checkbox_to_sql_bool($_POST["ff_xmllint_lint"]));
	}
	
	function hook_prefs_tab($args) {
		if ($args != "prefPrefs") return;
		
		print "<div dojoType=\"dijit.layout.AccordionPane\" title=\"".__("XML Tidy and Lint")."\">";
		print "<br/>";
		
		$tidyEnabled = sql_bool_to_bool($this->host->get($this, "tidy", bool_to_sql_bool(FALSE)));
		$lintEnabled = sql_bool_to_bool($this->host->get($this, "lint", bool_to_sql_bool(TRUE)));
		if ($tidyEnabled) {
			$tidyChecked = "checked=\"1\"";
		} else {
			$tidyChecked = "";
		}
		if ($lintEnabled) {
			$lintChecked = "checked=\"1\"";
		} else {
			$lintChecked = "";
		}
		
		print "<form dojoType=\"dijit.form.Form\">";
		
		print "<script type=\"dojo/method\" event=\"onSubmit\" args=\"evt\">
			evt.preventDefault();
			if (this.validate()) {
				console.log(dojo.objectToQuery(this.getValues()));
				new Ajax.Request('backend.php', {
					parameters: dojo.objectToQuery(this.getValues()),
					onComplete: function(transport) {
						notify_info(transport.responseText);
					}
				});
				//this.reset();
			}
			</script>";

		print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"op\" value=\"pluginhandler\">";
		print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"method\" value=\"save\">";
		print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"plugin\" value=\"ff_xmllint\">";
		
		print "<table width=\"100%\" class=\"prefPrefsList\">";
		
		print "<tr><td width=\"40%\"><label for=\"ff_xmllint_tidy\">".__("Enable Tidy")."</label></td>";
		print "<td class=\"prefValue\"><input dojoType=\"dijit.form.CheckBox\" type=\"checkbox\" name=\"ff_xmllint_tidy\" id=\"ff_xmllint_tidy\" $tidyChecked></td></tr>";
		print "<tr><td width=\"40%\"><label for=\"ff_xmllint_lint\">".__("Enable Lint")."</label></td>";
		print "<td class=\"prefValue\"><input dojoType=\"dijit.form.CheckBox\" type=\"checkbox\" name=\"ff_xmllint_lint\" id=\"ff_xmllint_lint\" $lintChecked></td></tr>";

		print "</table>";

		print "<p><button dojoType=\"dijit.form.Button\" type=\"submit\">".
			__("Set values")."</button>";

		print "</form>";
		
		print "</div>"; #pane
	}
	
	function hook_feed_fetched($feed_data) {
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("file", "/dev/null", "a")
		);
		
		$tidyEnabled = sql_bool_to_bool($this->host->get($this, "tidy", bool_to_sql_bool(FALSE)));
		$lintEnabled = sql_bool_to_bool($this->host->get($this, "lint", bool_to_sql_bool(TRUE)));
		
		if ($tidyEnabled) {
			$process = proc_open('tidy --xml -', $descriptorspec, $pipes);
			
			if (is_resource($process)) {
				fwrite($pipes[0], $feed_data);
				fclose($pipes[0]);
				$new_feed_data = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
				
				$return_value = proc_close($process);
				if ($return_value == 0)
					$feed_data = $new_feed_data;
			}
		}
		
		if ($lintEnabled) {
			$process = proc_open('xmllint --recover -', $descriptorspec, $pipes);
			
			if (is_resource($process)) {
				fwrite($pipes[0], $feed_data);
				fclose($pipes[0]);
				$new_feed_data = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
				
				$return_value = proc_close($process);
				if ($return_value == 0)
					$feed_data = $new_feed_data;
			}
		}
		
		return $feed_data;
	}

}
?>
