<h1>
	Getting all bookmarks with certain tags
	<!-- Okay, well here's something interesting. Certain output functions only work inside of the php shorthand tag.  Why is that?  -->
	<!-- Ohhhhhhh, apparently the < ? = tag is actually a short hand for echo!  Didn't know that, good to know. -->
	<!-- Okay, so the Text class apparently pulls back a text version of all the tags in the tag array, that's convenient -->
	<?= $this->Text->toList($tags); ?>
</h1>

<section>
	<?php
		// Iterating over all of the bookmarks that we added to the session in our controller
		foreach($bookmarks as $bookmark){ ?>
		<article>
			<!-- Using the HTML helper to generate a URL using the title and URL stored in our bookmark entity objects -->
			<h4><?= $this->Html->link($bookmark->title, $bookmark->url); ?></h4>

			<!-- The h() function is a convenience method for calling htmlspecialchars() in PHP, good for outputting URLs, variables, etc. -->
			<small><?= h($bookmark->url); ?></small>

			<!-- And finally, the coup de grace!  Just outputting the description as a paragraph, with the paragraph tags!  Check the html!  --!>
			<?= $this->Text->autoParagraph($bookmark->description); ?>
		</article>
	<?php
		}
	?>
</section>
