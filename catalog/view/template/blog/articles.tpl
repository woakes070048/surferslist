<div class="progress-bar hidden"></div>
<div class="information loading hidden">
	<p><?php echo $text_loading; ?></p>
	<span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>
</div>
<div class="list-articles">
	<?php foreach ($articles as $article) { ?>
	<article class="widget blog-article">
		<div class="content widget-no-heading clearafter">
			<?php if ($article['thumb']){ ?>
			<div class="image image-border article-image grid-4 hidden-small">
				<a href="<?php echo $article['href'];?>">
					<img src="<?php echo $article['thumb']; ?>" />
				</a>
			</div>
			<?php } ?>
			<div class="article-preview grid-8">
				<header>
					<div class="article-date">
						<span class="month"><?php echo $article['month']; ?></span>
						<span class="day"><?php echo $article['day']; ?></span>
						<span class="year"><?php echo $article['year']; ?></span>
					</div>
					<div class="article-heading">
						<h2 class="article-name">
							<a href="<?php echo $article['href'];?>"><?php echo $article['name'];?></a>
						</h2>
						<div class="article-stats">
							<i class="fa fa-user"></i> <?php echo $text_author; ?> <a href="<?php echo $article['author_search']; ?>"><?php echo $article['author_name']; ?></a>
							| <i class="fa fa-eye"></i> <?php echo $text_viewed; ?> <?php echo $article['viewed']; ?>
						</div>
					</div>
				</header>

				<div class="article-description">
					<?php echo $article['short_description'];?>
				</div>

				<footer class="read-more">
					<a class="button button_inverse" href="<?php echo $article['href'];?>"><?php echo $button_read_more;?></a>
				</footer>
			</div>
		</div>
	</article>
	<?php } ?>
</div>
