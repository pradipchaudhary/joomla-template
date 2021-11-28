<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// no direct access
defined('_JEXEC') or die;
?>
<?php if ($datasource != 'articles') : ?>
	<div class="alert alert-error"><?php echo JText::_('MOD_LATESTNEWSENHANCEDEXTENDED_ERROR_WRONGLAYOUT'); ?></div>
<?php elseif (empty($list)) : ?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee nonews<?php echo $isMobile ? ' mobile' : ''; ?>"><div class="alert alert-info"><?php echo $nodata_message; ?></div></div>
<?php else : ?>
	<?php
		$categories = modLatestNewsEnhancedExtendedHelperStandard::getCategoryList($params, $list);
		$nbr_cat = count($categories);
		
		if ($remove_whitespaces) {
			ob_start(function($buffer) { return preg_replace('/\s+/', ' ', $buffer); });
		}
		
		$i = 0;
		$current_catid = $list[0]->catid;
		$new_catid = true;
	?>
	<div id="lnee_<?php echo $class_suffix; ?>" class="lnee newslist<?php echo $isMobile ? ' mobile' : ''; ?> <?php echo $alignment; ?>">
		
		<?php if (trim($params->get('pretext', ''))) : ?>
			<div class="pretext">
				<?php echo $params->get('pretext');	?>
			</div>
		<?php endif; ?>	
		
		<?php if (!empty($readall_link) && $pos_readall == 'first') : ?>
			<div class="readalllink first<?php echo $extrareadallclass; ?>"<?php echo $extrareadallstyle; ?>>
				<a href="<?php echo $readall_link; ?>" title="<?php echo $readall_link_label ?>" class="hasTooltip<?php echo $extrareadalllinkclass; ?>"<?php echo $readall_isExternal ? ' target="_blank"' : ''; ?>><span><?php echo $readall_link_label ?></span></a>
			</div>
		<?php endif; ?>			
		<?php if ($show_category && $pos_category == 'first' && $nbr_cat == 1 && $consolidate_category) : ?>
			<?php 
				if ($list[0]->category_authorized) {
					$cat_label = empty($cat_link_text) ? $list[0]->category_title : $cat_link_text;
				} else {
					$cat_label = empty($unauthorized_cat_link_text) ? $list[0]->category_title : $unauthorized_cat_link_text;
				}
			?>
			<div class="onecatlink first<?php echo $extracategoryclass; ?>"<?php echo $extracategorystyle; ?>>
				<?php if ($link_category) : ?>
					<a href="<?php echo $list[0]->catlink; ?>" title="<?php echo $cat_label; ?>" class="hasTooltip<?php echo $extracategorylinkclass; ?>">
				<?php endif; ?>
					<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
					<?php if ($show_article_count) : ?>&nbsp;<span class="article_count label label-info"><?php echo $categories[$current_catid]->count; ?></span><?php endif; ?>
				<?php if ($link_category) : ?>
					</a>
				<?php endif; ?>		
				<?php if ($show_category_description) : ?>
					<div class="category_description"><?php echo $categories[$current_catid]->description; ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ($animation) : ?>	
			<?php if (!empty($pagination) && ($pagination_position_type == 'above' || $pagination_position_type == 'around')) : ?>
				<?php if (JFile::exists(dirname(__FILE__).'/pagination/'.$animation.'.php')) : ?>
					<?php $pagination_position = $pagination_position_top; ?>
					<?php include 'pagination/'.$animation.'.php'; ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($leading_items_count > 0) : ?>
		<ul class="latestnews-items altered">
		<?php else : ?>
		<ul class="latestnews-items">
		<?php endif; ?>
			<?php foreach ($list as $item) :  ?>
				<?php
					$extraclasses = ($i % 2) ? " even" : " odd";
				
					if ($show_image || $show_calendar) {
						switch ($text_align) {
							case 'l' : $extraclasses .= " head_right"; break;
							case 'r' : $extraclasses .= " head_left"; break;
							case 'lr' : $extraclasses .= ($i % 2) ? " head_left" : " head_right"; break;
							case 'rl' : $extraclasses .= ($i % 2) ? " head_right" : " head_left"; break;
								
							case 't' : $extraclasses .= " text_top"; break;
							case 'b' : $extraclasses .= " text_bottom"; break;
							case 'bt' : $extraclasses .= ($i % 2) ? " text_top" : " text_bottom"; break;
							case 'tb' : $extraclasses .= ($i % 2) ? " text_bottom" : " text_top"; break;
							default :
								
							break;
						}
					}
					
					$i++;					
					
					if ($i > 1) {
						if ($current_catid != $item->catid) {
							$current_catid = $item->catid;
							$new_catid = true;
						} else {
							$new_catid = false;
						}
					}
					
					if ($item->category_authorized) {
						$cat_label = empty($cat_link_text) ? $item->category_title : $cat_link_text;
					} else {
						$cat_label = empty($unauthorized_cat_link_text) ? $item->category_title : $unauthorized_cat_link_text;
					}					
					
					// if the link is link a..c, replace the label with the text for the links a..c
					// WARNING: 'linktitle' can be the link and not the text of link a..c (in case the text was missing) 
					// -> changed the behavior in helper_standard so that 'linktitle' is 'title' when text is missing
					// note: $item->linktitle and $item->title will always be different if the title is truncated ->strpos (trim the dots)
					
					if ($show_link_label && $item->link) {
						$link_label_item = $item->authorized ? $link_label : $unauthorized_link_label ;
						if (strpos($item->linktitle, rtrim($item->title, '.')) === false) {
							$link_label_item = $item->linktitle;
						}
					}
					
					$registry_attribs = new JRegistry;
					$registry_attribs->loadString($item->attribs);
					
					$details = modLatestNewsEnhancedExtendedHelper::getDetails($params);
					$info_block = modLatestNewsEnhancedExtendedHelper::getInfoBlock($details, $params, $item, $registry_attribs);				
									
					// check if the link is the same of the article activaly shown 
					$css_active = '';
					$option = $app->input->get('option');
					$view = $app->input->get('view');		
					if ($option === 'com_content' && $view === 'article') {
						$current_id = $app->input->getInt('id');
						if ($current_id == $item->id) {
							$css_active = ' active';
						}
					}
					
					$css_limited = '';
					if ($leading_items_count > 0) {
						if ($i > $leading_items_count) {
							$css_limited = ' downgraded';
						} else {
							$css_limited = ' full';
						}
					}
	
					$css_shadow = '';
					if ($show_image && $shadow_width_pic > 0) {
						if (!($leading_items_count > 0 && $i > $leading_items_count && $remove_head)) {
							$css_shadow = ' shadow simple';
						}
					}
					
					$css_hover = '';
					if ($show_image && $hover_effect != 'none' && $show_link && $item->link) {
						$css_hover = ' '.$hover_effect;
					}
					
					$css_featured = '';
					if ($item->featured) {
						$css_featured = ' featured';
					}
				?>
				<li id="latestnews-item-<?php echo $item->id; ?>" class="latestnews-item catid-<?php echo $item->catid; ?><?php echo $css_active; ?><?php echo $css_limited; ?><?php echo $css_shadow; ?><?php echo $css_featured; ?>">			
					<?php if ($show_errors && !empty($item->error)) : ?>
						<div class="alert alert-error">
		  					<button type="button" class="close" data-dismiss="alert">&times;</button>					
							<?php foreach ($item->error as $error) :  ?>							
		  						<?php echo JText::_('COM_CONTENT_CONTENT_TYPE_ARTICLE').' id '.$item->id.': '.$error; ?>
		  					<?php endforeach; ?>
						</div>
					<?php endif; ?>				
					<div class="news<?php echo $extraclasses ?>">
						<div class="innernews">
							<?php if ($show_category && $pos_category == 'first' && (($nbr_cat > 1 && $consolidate_category && $new_catid) || !$consolidate_category)) : ?>
								<div class="catlink<?php echo $extracategoryclass; ?>"<?php echo $extracategorystyle; ?>>		
									<?php if ($link_category) : ?>						
										<a href="<?php echo $item->catlink; ?>" title="<?php echo $cat_label; ?>" class="hasTooltip<?php echo $extracategorylinkclass; ?>">
									<?php endif; ?>
										<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
										<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count label label-info"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>	
									<?php if ($link_category) : ?>						
										</a>
									<?php endif; ?>								
								</div>
							<?php endif; ?>					
							<?php if ($show_category && $pos_category == 'first' && ($nbr_cat > 1 && $consolidate_category && !$new_catid)) : ?>
								<div class="catlink emptyspace<?php echo $extracategoryclass; ?>"<?php echo $extracategorystyle; ?>>
									<?php if ($link_category) : ?>						
										<a href="<?php echo $item->catlink; ?>" title="<?php echo $cat_label; ?>" class="hasTooltip<?php echo $extracategorylinkclass; ?>">
									<?php endif; ?>
										<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
									<?php if ($link_category) : ?>						
										</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>										
							<?php if ($title_before_head) : ?>
								<div class="newsinfooverhead">
									<?php if ($remove_details && $leading_items_count > 0 && $i > $leading_items_count) : ?>
									<?php else : ?>
										<?php if (!empty($info_block) && $info_block_placement == 0) : ?>
											<?php echo $info_block; ?>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($show_title) : ?>
										<h<?php echo $title_html_tag; ?> class="newstitle">
											<?php if ($show_link) : ?>
												<?php if ($item->link) : ?>
													<?php echo modLatestNewsEnhancedExtendedHelper::getATag($item, $follow, true, $popup_width, $popup_height); ?>
														<span><?php echo $item->title; ?></span>
													</a>
												<?php else : ?>
													<span><?php echo $item->title; ?></span>
												<?php endif; ?>	
											<?php else : ?>
												<span><?php echo $item->title; ?></span>
											<?php endif; ?>
										</h<?php echo $title_html_tag; ?>>
									<?php endif; ?>
									<?php if (!empty($info_block) && $info_block_placement == 1) : ?>
										<?php echo $info_block; ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ($remove_head && $leading_items_count > 0 && $i > $leading_items_count) : ?>
							<?php else : ?>
								<?php if ($show_image) : ?>
									<?php if (!empty($item->imagetag) || $keep_space) : ?>	
										<div class="newshead picturetype<?php echo $css_hover; ?>">
											<?php if (!empty($item->imagetag)) : ?>
												<div class="picture">
											<?php elseif ($keep_space) : ?>		
												<div class="nopicture">
											<?php endif; ?>										
												<?php if (!empty($item->imagetag)) : ?>
													<div class="innerpicture">
														<?php if ($show_link && $item->link) : ?>
															<?php echo modLatestNewsEnhancedExtendedHelper::getATag($item, $follow, true, $popup_width, $popup_height); ?>
																<?php echo $item->imagetag; ?>
															</a>
														<?php else : ?>
															<?php echo $item->imagetag; ?>
														<?php endif; ?>
													</div>
												<?php elseif ($keep_space) : ?>
													<?php if ($show_link && $item->link) : ?>
														<?php echo modLatestNewsEnhancedExtendedHelper::getATag($item, $follow, true, $popup_width, $popup_height); ?>
															<span></span>
														</a>
													<?php else : ?>
														<span></span>
													<?php endif; ?>
												<?php endif; ?>
												</div>
										</div>		
									<?php endif; ?>
								<?php elseif ($show_calendar) : ?>	
									<?php if (!empty($item->date) || $keep_space) : ?>
										<div class="newshead calendartype">
											<?php if (!empty($item->date)) : ?>			
												<div class="calendar <?php echo $extracalendarclass; ?>">		
													<?php $date_params = modLatestNewsEnhancedExtendedCalendarHelper::getCalendarBlockData($params, $item->date); ?>											
													<?php foreach ($date_params as $counter => $date_array) : ?>
														<?php if (!empty($date_array)) : ?>
															<span class="position<?php echo ($counter + 1); ?> <?php echo key($date_array); ?>"><?php echo $date_array[key($date_array)]; ?></span>
														<?php endif; ?>
													<?php endforeach; ?>							
												</div>
											<?php elseif ($keep_space) : ?>	
												<div class="calendar nodate"></div>
											<?php endif; ?>	
										</div>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ($show_image && empty($item->imagetag) && !$keep_space) : ?>
								<div class="newsinfo noimagespace">
							<?php else : ?>
								<div class="newsinfo">
							<?php endif; ?>
								<?php if ($show_category && $pos_category == 'title' && (($consolidate_category && $new_catid) || !$consolidate_category)) : ?>
									<p class="catlink<?php echo $extracategoryclass; ?>"<?php echo $extracategorystyle; ?>>		
										<?php if ($link_category) : ?>						
											<a href="<?php echo $item->catlink; ?>" title="<?php echo $cat_label; ?>" class="hasTooltip<?php echo $extracategorylinkclass; ?>">
										<?php endif; ?>
											<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>										
											<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count label label-info"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>	
										<?php if ($link_category) : ?>						
											</a>
										<?php endif; ?>
									</p>
								<?php endif; ?>	
								<?php if (!$title_before_head) : ?>
									<?php if ($remove_details && $leading_items_count > 0 && $i > $leading_items_count) : ?>
									<?php else : ?>
										<?php if (!empty($info_block) && $info_block_placement == 0) : ?>
											<?php echo $info_block; ?>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($show_title) : ?>
										<h<?php echo $title_html_tag; ?> class="newstitle">
											<?php if ($show_link) : ?>
												<?php if ($item->link) : ?>
													<?php echo modLatestNewsEnhancedExtendedHelper::getATag($item, $follow, true, $popup_width, $popup_height); ?>
														<span><?php echo $item->title; ?></span>
													</a>
												<?php else : ?>
													<span><?php echo $item->title; ?></span>
												<?php endif; ?>	
											<?php else : ?>
												<span><?php echo $item->title; ?></span>
											<?php endif; ?>
										</h<?php echo $title_html_tag; ?>>
									<?php endif; ?>			
								<?php endif; ?>
								<?php if ($remove_details && $leading_items_count > 0 && $i > $leading_items_count) : ?>
								<?php else : ?>	
									<?php if (!empty($info_block) && ($info_block_placement == 3 || ($info_block_placement == 1 && !$title_before_head))) : ?>
										<?php echo $info_block; ?>
									<?php endif; ?>	
								<?php endif; ?>
								<?php if ($remove_text && $leading_items_count > 0 && $i > $leading_items_count) : ?>
								<?php else : ?>
									<?php if (!empty($item->text)) : ?>
										<div class="newsintro">
											<?php echo $item->text; ?>
											<?php if ($show_link_label && $append_link && !empty($link_label_item) && $item->cropped) : ?>
												<?php if ($item->link) : ?>
													<?php echo modLatestNewsEnhancedExtendedHelper::getATag($item, $follow, true, $popup_width, $popup_height); ?>
														<span><?php echo $link_label_item; ?></span>
													</a>
												<?php endif; ?>	
											<?php endif; ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($remove_details && $leading_items_count > 0 && $i > $leading_items_count) : ?>
								<?php else : ?>	
									<?php if (!empty($info_block) && $info_block_placement == 2) : ?>
										<?php echo $info_block; ?>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($show_link_label && !$append_link && !empty($link_label_item) && $item->cropped) : ?>
									<?php if ($item->link) : ?>								
										<p class="link<?php echo $extrareadmoreclass; ?>"<?php echo $extrareadmorestyle; ?>>
											<?php echo modLatestNewsEnhancedExtendedHelper::getATag($item, $follow, true, $popup_width, $popup_height, $extrareadmorelinkclass); ?>
												<span><?php echo $link_label_item; ?></span>
											</a>
										</p>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ($show_category && $pos_category == 'last' && (($nbr_cat > 1 && $consolidate_category && $new_catid) || !$consolidate_category)) : ?>
									<p class="catlink<?php echo $extracategoryclass; ?>"<?php echo $extracategorystyle; ?>>		
										<?php if ($link_category) : ?>						
											<a href="<?php echo $item->catlink; ?>" title="<?php echo $cat_label; ?>" class="hasTooltip<?php echo $extracategorylinkclass; ?>">
										<?php endif; ?>
											<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
											<?php if ($show_article_count && $consolidate_category) : ?>&nbsp;<span class="article_count label label-info"><?php echo $categories[$item->catid]->count; ?></span><?php endif; ?>	
										<?php if ($link_category) : ?>						
											</a>
										<?php endif; ?>
									</p>
								<?php endif; ?>							
							</div>	
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($animation) : ?>	
			<?php if (!empty($pagination) && ($pagination_position_type == 'below' || $pagination_position_type == 'around')) : ?>
				<?php if (JFile::exists(dirname(__FILE__).'/pagination/'.$animation.'.php')) : ?>
					<div class="clearfix"></div>
					<?php $pagination_position = $pagination_position_bottom; ?>
					<?php include 'pagination/'.$animation.'.php'; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($show_category && $pos_category == 'last' && $nbr_cat == 1 && $consolidate_category) : ?>
			<?php 
				if ($list[0]->category_authorized) {
					$cat_label = empty($cat_link_text) ? $list[0]->category_title : $cat_link_text;
				} else {
					$cat_label = empty($unauthorized_cat_link_text) ? $list[0]->category_title : $unauthorized_cat_link_text;
				}
			?>
			<div class="onecatlink last<?php echo $extracategoryclass; ?>"<?php echo $extracategorystyle; ?>>
				<?php if ($link_category) : ?>
					<a href="<?php echo $list[0]->catlink; ?>" title="<?php echo $cat_label; ?>" class="hasTooltip<?php echo $extracategorylinkclass; ?>">
				<?php endif; ?>
					<span<?php echo $extracategorynolinkclass; ?>><?php echo $cat_label; ?></span>
					<?php if ($show_article_count) : ?>&nbsp;<span class="article_count label label-info"><?php echo $categories[$current_catid]->count; ?></span><?php endif; ?>
				<?php if ($link_category) : ?>
					</a>
				<?php endif; ?>
				<?php if ($show_category_description) : ?>
					<div class="category_description"><?php echo $categories[$current_catid]->description; ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>	
		<?php if (!empty($readall_link) && $pos_readall == 'last') : ?>
			<div class="readalllink last<?php echo $extrareadallclass; ?>"<?php echo $extrareadallstyle; ?>>
				<a href="<?php echo $readall_link; ?>" title="<?php echo $readall_link_label ?>" class="hasTooltip<?php echo $extrareadalllinkclass; ?>"<?php echo $readall_isExternal ? ' target="_blank"' : ''; ?>><span><?php echo $readall_link_label ?></span></a>
			</div>
		<?php endif; ?>
		
		<?php if (trim($params->get('posttext', ''))) : ?>
			<div class="posttext">			
				<?php echo $params->get('posttext'); ?>
			</div>
		<?php endif; ?>

	</div>
	<?php if ($remove_whitespaces) : ?>
		<?php ob_get_flush(); ?>
	<?php endif; ?>
<?php endif; ?>