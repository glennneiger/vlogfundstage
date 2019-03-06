				</section>
			</div><!--/.container-fluid-->
		</div><!--/.ddl-full-width-row-->
	</div><!--/.container-fluid-->
	<div class="container-fluid">
		<div class="ddl-full-width-row row">
        	<div class="col-sm-12">
				<footer id="colophon" class="site-footer" role="contentinfo">
					<div class="sf-footer-social">
						<h3>like and subscribe: @vlogfunding</h3>
						<a class="sf-footer-social-link" target="_blank" href="https://facebook.com/vlogfunding"><i class="fab fa-facebook"></i></a>
						<a class="sf-footer-social-link" target="_blank" href="https://twitter.com/vlogfunding"><i class="fab fa-twitter"></i></a>
						<a class="sf-footer-social-link" target="_blank" href="https://www.instagram.com/vlogfunding/"><i class="fab fa-instagram"></i></a>
						<?php //<!--<a class="sf-footer-social-link" target="_blank" href="#"><i class="fa fa-pinterest"></i></a>--> ?>
						<a class="sf-footer-social-link" target="_blank" href="https://www.youtube.com/channel/UC9oxutUco_w8d1X9aSGLNiA"><i class="fab fa-youtube"></i></a>
					</div><!--/.sf-footer-social-->
					<ul class="sf-footer-navigation">
						<li class="sf-footer-navigation-head">
							<a href="#" class="sf-footer-navigation-head-link">About Us</a>
							<ul class="sf-footer-navigation-sub">
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/about" class="sf-footer-navigation-child-link">What is <?php echo do_shortcode('[wpv-bloginfo]');?>?</a></li>
							</ul>
						</li>
						<li class="sf-footer-navigation-head">
							<a href="/faq" class="sf-footer-navigation-head-link">Help</a>
							<ul class="sf-footer-navigation-sub">
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/faq" class="sf-footer-navigation-child-link">FAQ</a></li>
								<?php /*<!--<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="#" class="sf-footer-navigation-child-link">Our Rules</a></li>-->
									<!--<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="#" class="sf-footer-navigation-child-link">Creator Handbook</a></li>-->
									<!--<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="#" class="sf-footer-navigation-child-link">Trust & Safety</a></li>-->*/?>            
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/terms" class="sf-footer-navigation-child-link">Terms of Use</a></li>
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/privacy" class="sf-footer-navigation-child-link">Privacy Policy</a></li>
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/privacy#cookies" class="sf-footer-navigation-child-link">Cookie Policy</a></li>
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/rules-and-guidelines" class="sf-footer-navigation-child-link">Rules & Guidelines</a></li>
							</ul>
						</li>
						<li class="sf-footer-navigation-head">
							<a href="#" class="sf-footer-navigation-head-link">Discover</a>
							<ul class="sf-footer-navigation-sub">
								<?php if( is_user_logged_in() ) : //Check User Logged In ?>
									<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/campaign-form" id="create_campaign" class="sf-footer-navigation-child-link">Create a Collab</a></li>
								<?php else : //Else ?>
									<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/campaign-form-get-started" class="sf-footer-navigation-child-link">Start a Collab</a></li>
								<?php endif; //Endif ?>
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/youtube-collaborations" class="sf-footer-navigation-child-link">Collaborations</a></li>
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/blog" class="sf-footer-navigation-child-link">Blog</a></li> 
								<?php if( vlogfund_smile_mode_on() ) : //Check Smile Mode ?>
									<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="/organization" class="sf-footer-navigation-child-link">Organizations</a></li>
								<?php endif; //Endif ?>
								<li class="sf-footer-navigation-child"><i class="fa fa-angle-right"></i> <a href="#" class="sf-footer-navigation-child-link">More Coming soon</a></li>
							</ul>
						</li>      
						<li class="sf-footer-navigation-head">
							<a href="#" class="sf-footer-navigation-ead-link"></a>        
							Switch to <strong>Vlogfund Smile Mode</strong> <br>and support your favorite charitable organization <br>everytime you back a collaboration 😉
							<ul class="sf-footer-navigation-sub">        
								<li class="sf-footer-navigation-child">
									<label class="sf-smile-switch">
										<?php if( vlogfund_smile_mode_on() ) : //Check Smile Mode ?>
											<input type="checkbox" checked="checked">
										<?php else : //Else ?>
											<input type="checkbox">
										<?php endif; //Endif ?>
										<span class="sf-smile-slider"></span>
									</label>
								</li>
							</ul>
						</li>
					</ul>
					<div class="sf-footer-end">
						<div class="sf-footer-copyright">
							<span class="sf-footer-copyright-link">&copy; Copyright <?php echo date('Y');?> - Vlogfund All rights reserved </span>
						</div><!--/.sf-footer-copyright-->
					</div><!--/.sf-footer-end-->
				</footer>
			</div><!--/.container-fluid-->
		</div><!--/.ddl-full-width-row-->
	</div><!--/.container-fluid-->
	<?php wp_footer(); ?>
</div><!-- .wrapper -->
</body>
</html>