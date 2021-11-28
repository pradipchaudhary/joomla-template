<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div>
    <table class="table table-condensed">		
		<tbody>
			<?php if ($show_gif) : ?>
                <?php if (imagetypes() & IMG_GIF) : ?>
                    <tr>
                        <td><?php echo JText::_('LIB_SYW_GDTEST_GIF_SUPPORT'); ?></td>
                        <td style="text-align: right"><span class="label label-success"><?php echo JText::_('JENABLED'); ?></span></td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td><?php echo JText::_('LIB_SYW_GDTEST_GIF_SUPPORT'); ?></td>
                        <td style="text-align: right"><span class="label label-important"><?php echo JText::_('JDISABLED'); ?></span></td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
			<?php if (imagetypes() & IMG_JPG) : ?>
				<tr>
					<td><?php echo JText::_('LIB_SYW_GDTEST_JPG_SUPPORT'); ?></td>
					<td style="text-align: right"><span class="label label-success"><?php echo JText::_('JENABLED'); ?></span></td>
				</tr>
			<?php else : ?>
				<tr>
					<td><?php echo JText::_('LIB_SYW_GDTEST_JPG_SUPPORT'); ?></td>
					<td style="text-align: right"><span class="label label-important"><?php echo JText::_('JDISABLED'); ?></span></td>
				</tr>
			<?php endif; ?>
			<?php if (imagetypes() & IMG_PNG) : ?>
				<tr>
					<td><?php echo JText::_('LIB_SYW_GDTEST_PNG_SUPPORT'); ?></td>
					<td style="text-align: right"><span class="label label-success"><?php echo JText::_('JENABLED'); ?></span></td>
				</tr>
			<?php else : ?>
				<tr>
					<td><?php echo JText::_('LIB_SYW_GDTEST_PNG_SUPPORT'); ?></td>
					<td style="text-align: right"><span class="label label-important"><?php echo JText::_('JDISABLED'); ?></span></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
