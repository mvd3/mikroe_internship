<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		// echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

		echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js');
		echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js');
		echo $this->Html->script('kickstart/kickstart.js');
		echo $this->Html->css('kickstart/kickstart.css');
	?>
	<style>
	button a {color:white;text-decoration:none;}
	a {color:black;text-decoration:none;}
	body{
		margin:0;
		padding:0 1.5% 0 1.5%;
		color:#000;
		background:#efefef url(css/img/gray_jean.png);
		font:normal 0.9em/150% 'Arimo', arial, verdana, sans-serif;
		text-shadow: 0 0 1px transparent; /* google font pixelation fix */
	}
	</style>
</head>
<body>
	<div id="container">
		<div style="height:100px;">
			<ul class="menu">
				<li ><?php echo $this->Html->link(__('Measueremnt Units'), array('action' => 'index', 'controller' => 'MeasurementUnits')); ?></li>
				<li><?php echo $this->Html->link(__('Items'), array('action' => 'index', 'controller' => 'Items')); ?><ul>
						<li ><?php echo $this->Html->link(__('Item Types'), array('action' => 'index', 'controller' => 'ItemTypes')); ?></li>
						<li ><?php echo $this->Html->link(__('Materials'), array('action' => 'index', 'controller' => 'Materials')); ?></li>
						<li ><?php echo $this->Html->link(__('Semi Products'), array('action' => 'index', 'controller' => 'SemiProducts')); ?></li>
						<li ><?php echo $this->Html->link(__('Products'), array('action' => 'index', 'controller' => 'Products')); ?></li>
						<li ><?php echo $this->Html->link(__('Goods'), array('action' => 'index', 'controller' => 'Goods')); ?></li>
						<li ><?php echo $this->Html->link(__('Kits'), array('action' => 'index', 'controller' => 'Kits')); ?></li>
						<li ><?php echo $this->Html->link(__('Consumables'), array('action' => 'index', 'controller' => 'Consumables')); ?></li>
						<li ><?php echo $this->Html->link(__('Service Suppliers'), array('action' => 'index', 'controller' => 'ServiceSuppliers')); ?></li>
						<li ><?php echo $this->Html->link(__('Service Products'), array('action' => 'index', 'controller' => 'ServiceProducts')); ?></li>
						<li ><?php echo $this->Html->link(__('Inventories'), array('action' => 'index', 'controller' => 'Inventories')); ?></li>
					</ul>
				</li>
				<li><?php echo $this->Html->link(__('Warehouses'), array('action' => 'index', 'controller' => 'Warehouses')); ?></a>
					<ul>
						<li><?php echo $this->Html->link(__('Locations'), array('action' => 'index', 'controller' => 'WarehouseLocations')); ?></li>
						<li><?php echo $this->Html->link(__('Addresses'), array('action' => 'index', 'controller' => 'WarehouseAddresses')); ?></li>
						<li><?php echo $this->Html->link(__('Article Addresses'), array('action' => 'index', 'controller' => 'ArticleAddresses')); ?></li>
						<li><?php echo $this->Html->link(__('Articles in Warehouse'), array('action' => 'index', 'controller' => 'WarehouseArticles')); ?></li>
						<li><?php echo $this->Html->link(__('Moves'), array('action' => 'index', 'controller' => 'MoveCards')); ?></li>
						<li><?php echo $this->Html->link(__('Move allowances'), array('action' => 'index', 'controller' => 'MoveAllowances')); ?></li>
					</ul>
				</li>
				<li ><?php echo $this->Html->link(__('Users'), array('action' => 'index', 'controller' => 'Users')); ?></li>
				<li ><?php echo $this->Html->link(__('Groups'), array('action' => 'index', 'controller' => 'Groups')); ?></li>
				<li ><?php echo $this->Html->link(__('Log out'), array('action' => 'logout', 'controller' => 'Users')); ?></li>
			</ul>
		</div>
		<!-- <div id="header">
			<h1><?php //echo $this->Html->link($cakeDescription, 'https://cakephp.org'); ?></h1>
		</div> -->
		<div id="content">
			<?php echo $this->fetch('content'); ?>
			<?php echo $this->Flash->render(); ?>
		</div>
		<!-- <div id="footer">
			<?php /*echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'https://cakephp.org/',
					array('target' => '_blank', 'escape' => false, 'id' => 'cake-powered')
				);*/
			?>
			<p>
				<?php //echo $cakeVersion; ?>
			</p>
		</div> -->
	</div>
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
