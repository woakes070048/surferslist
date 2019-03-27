<div class="progress-bar hidden"></div>
<div class="information loading hidden">
	<p><?php echo $text_loading; ?></p>
	<span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>
</div>
<div class="list-articles">
	<?php foreach ($articles as $article) { ?>
	<article class="widget blog-article">
		<div class="content widget-no-heading clearafter">
			<?php if ($article['thumb']) { ?>
			<div class="grid-3">
				<div class="article-image image image-border">
					<a href="<?php echo $article['href'];?>">
						<img src="<?php echo $article['thumb']; ?>" />
					</a>
				</div>
			</div>
			<?php } ?>
			<div class="grid-6">
				<div class="article-preview">
					<header class="article-heading">
						<div class="article-date">
							<span class="month"><?php echo $article['month']; ?></span>
							<span class="day"><?php echo $article['day']; ?></span>
							<span class="year"><?php echo $article['year']; ?></span>
						</div>
						<h2 class="article-name">
							<a href="<?php echo $article['href'];?>"><?php echo $article['name'];?></a>
						</h2>
						<div class="article-stats">
							<span class="author"><i class="fa fa-user"></i><?php echo $text_author; ?> <a href="<?php echo $article['author_search']; ?>"><?php echo $article['author_name']; ?></a></span>
							<span class="published hidden"><i class="fa fa-calendar"></i><?php echo $text_published; ?> <?php echo $article['date_published']; ?></span>
							<?php if ($article['categories']) { ?>
							<span class="categories"><i class="fa fa-sitemap"></i><?php echo $text_category; ?>
								<?php for ($i = 0; $i < count($article['categories']); $i++) { ?>
								<?php if ($i < (count($article['categories']) - 1)) { ?>
								<a href="<?php echo $article['categories'][$i]['href']; ?>"><?php echo $article['categories'][$i]['name']; ?></a>,
								<?php } else { ?>
								<a href="<?php echo $article['categories'][$i]['href']; ?>"><?php echo $article['categories'][$i]['name']; ?></a>
								<?php } ?>
								<?php } ?></span>
							<?php } ?>
						</div>
					</header>

					<div class="article-description">
						<?php echo $article['short_description'];?>
					</div>

					<footer>
						<a class="button button_inverse button_dark read-more" href="<?php echo $article['href'];?>"><?php echo $button_read_more;?></a>
					</footer>
				</div>
			</div>
		</div>
	</article>
	<?php } ?>
</div>
