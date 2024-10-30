<?php
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 02/10/2018
 * Time: 18:55
 */
defined('ABSPATH') or die("No script kiddies please!");
global $active_tab;
?>

<h2 class="nav-tab-wrapper"><?php _e('Donate', $this->plugin_name);?></h2>



<p>
	<p class="notice notice-success notice-large">
	<?php _e('If you like my work you can contribute with a small donation. It will help me to pay server charges and everything related to the development.', $this->plugin_name);?>

	</p>
	<p style="text-align: center;"><strong> <?php _e('Thank you!', $this->plugin_name);?></strong></p>

	<center><table class="widefat fixed" style="text-align: center;" cellspacing="0">
			<tbody>
			<tr>
				<th style="text-align: center;">Paypal.me</th>
                <th style="text-align: center;">Patreon</th>
				<th style="text-align: center;">Liberapay</th>
				<th style="text-align: center;">Ko-Fi</th>
			</tr>
			<tr>
				<td>
					<p style="text-align: center;"><a href="https://www.paypal.me/Mastalab"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate" /></a></p>
				</td>
                <td>
                    <p style="text-align: center;"><center><a href="https://www.patreon.com/bePatron?u=14085739" data-patreon-widget-type="become-patron-button">Become a Patron!</a><script async src="https://c6.patreon.com/becomePatronButton.bundle.js"></script></center></p>
                </td>
                <td>
					<p style="text-align: center;">
                            <script src="https://liberapay.com/tom79/widgets/button.js"></script>
                        <noscript><a href="https://liberapay.com/tom79/donate"><img alt="Donate using Liberapay" src="https://liberapay.com/assets/widgets/donate.svg"></a></noscript>
                        <br/><br/><img src="https://img.shields.io/liberapay/patrons/tom79.svg?logo=liberapay" />
                    </p>
				</td>
				<td><center><script type='text/javascript' src='https://ko-fi.com/widgets/widget_2.js'></script><script type='text/javascript'>kofiwidget2.init('Support Me on Ko-fi', '#46b798', 'D1D4K0HZ');kofiwidget2.draw();</script></center></td>
			</tr>
			</tbody>
		</table>
	</center>
</p>