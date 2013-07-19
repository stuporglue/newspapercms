<form class='searchbox' action='<?php print NPC_BASE_URL; ?>search/' method='post'>
    <input type="text" name='query' id='searchboxinput' <?php
    if(array_key_exists('query',$_POST)){
	print "value='" . htmlentities($_POST['query'])  . "'";
    }
    ?>/>
    <input id='searchbutton'type='submit' value='Search'/>
</form>