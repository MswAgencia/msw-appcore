<div class="alert alert-danger alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<?php 
		if(!is_array($message))
			echo $message;
		else
			echo implode('<br>', $message);
		
		if(isset($text) and isset($url)) 
			echo $this->Html->link($text, $url, ['class' => 'alert-link']);
	?>
</div>