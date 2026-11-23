<?php
/**
 * Sample content for `wp zeronkit-portfolio seed`.
 *
 * Everything here is neutral sample copy for a photography website: no real
 * people, no real places, no invented awards, clients or testimonials, and no
 * prices anyone has agreed to. %SITE% is replaced with the site title when the
 * content is inserted. The pictures are the flat sample artwork in
 * assets/samples, which is ZeronKit's own work (GPL).
 *
 * @package zeronkit-portfolio
 */

defined( 'ABSPATH' ) || exit;

return array(
	// Keys match assets/samples/sample-<key>.jpg
	'alts'     => array(
		'01' => 'Sample picture in warm grey tones with soft rounded shapes',
		'02' => 'Sample picture in cool grey tones with a wide band',
		'03' => 'Sample picture in sand tones with an arch',
		'04' => 'Sample picture in sage tones with a diagonal corner',
		'05' => 'Sample picture in lilac tones with a tall column',
		'06' => 'Sample picture in clay tones with a square',
		'07' => 'Sample picture in mist tones with a circle',
		'08' => 'Sample picture in stone tones with a horizontal band',
		'09' => 'Sample picture in paper tones with an arch',
		'10' => 'Sample picture in slate tones with a corner shape',
		'11' => 'Sample picture in ash tones with a column',
		'12' => 'Sample picture in linen tones with a square',
		'13' => 'Upright sample picture in warm grey tones',
		'14' => 'Upright sample picture in cool grey tones',
		'15' => 'Upright sample picture in sand tones',
		'16' => 'Upright sample picture in sage tones',
		'17' => 'Upright sample picture in lilac tones',
		'18' => 'Upright sample picture in clay tones',
		'19' => 'Upright sample picture in mist tones',
		'20' => 'Upright sample picture in stone tones',
		'21' => 'Sample picture in paper tones, four by five',
		'22' => 'Sample picture in slate tones, four by five',
		'23' => 'Sample picture in ash tones, four by five',
		'24' => 'Sample picture in linen tones, four by five',
		'25' => 'Sample picture in warm grey tones, four by five',
		'26' => 'Sample picture in cool grey tones, four by five',
		'27' => 'Square sample picture in sand tones',
		'28' => 'Square sample picture in sage tones',
		'29' => 'Square sample picture in lilac tones',
		'30' => 'Square sample picture in clay tones',
	),

	'hero'     => array( '01', '04', '13', '28' ),

	'projects' => array(
		array(
			'title'    => 'Golden Hour Portraits',
			'category' => 'Portraits',
			'type'     => 'Personal session',
			'place'    => 'Open country',
			'location' => 'Open fields, late summer',
			'date'     => '2026-08-14',
			'featured' => true,
			'photos'   => array( '13', '14', '21', '01', '27' ),
			'excerpt'  => 'A relaxed evening session built around the last hour of warm, low sunlight.',
			'content'  => "<!-- wp:paragraph --><p>This session starts an hour before sunset and finishes when the light is gone. There is no fixed shot list, only a walk through the fields with time to talk, pause and look at the light.</p><!-- /wp:paragraph -->\n<!-- wp:paragraph --><p>The aim is simple: natural portraits that still look like the person in them, with warm backlight doing most of the work.</p><!-- /wp:paragraph -->",
		),
		array(
			'title'    => 'Northern Coastlines',
			'category' => 'Landscapes',
			'type'     => 'Personal project',
			'place'    => 'Coast',
			'location' => 'A northern coastline',
			'date'     => '2026-05-02',
			'featured' => true,
			'photos'   => array( '02', '03', '04', '22', '28' ),
			'excerpt'  => 'Cold water, clear air and the long light of a northern spring.',
			'content'  => "<!-- wp:paragraph --><p>A slow week following a coastline with one camera and two lenses. Most days begin before sunrise and end long after the light has faded.</p><!-- /wp:paragraph -->\n<!-- wp:paragraph --><p>The pictures are quiet on purpose. They are about space, colour and the moments when the weather briefly opens up.</p><!-- /wp:paragraph -->",
		),
		array(
			'title'    => 'Lines of the City',
			'category' => 'Architecture',
			'type'     => 'Personal project',
			'place'    => 'City',
			'location' => 'City centre',
			'date'     => '2026-03-21',
			'featured' => true,
			'photos'   => array( '15', '16', '05', '23', '29' ),
			'excerpt'  => 'Glass, steel and repetition, photographed from the pavement looking up.',
			'content'  => '<!-- wp:paragraph --><p>A study of how modern buildings meet the sky. Every frame is made on foot during a single overcast weekend, when soft light keeps reflections calm.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'    => 'Quiet Rooms',
			'category' => 'Lifestyle',
			'type'     => 'Personal session',
			'place'    => 'Studio',
			'location' => 'At home',
			'date'     => '2026-02-08',
			'featured' => true,
			'photos'   => array( '17', '06', '24', '18', '30' ),
			'excerpt'  => 'Slow mornings at home, photographed as they happen.',
			'content'  => '<!-- wp:paragraph --><p>Lifestyle sessions at home are about ordinary moments: reading, waking up, the light moving across a room. Nothing is staged beyond opening the curtains.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'    => 'Mountain Light',
			'category' => 'Landscapes',
			'type'     => 'Personal project',
			'place'    => 'Mountains',
			'location' => 'High valleys',
			'date'     => '2025-10-11',
			'featured' => false,
			'photos'   => array( '07', '08', '09', '25', '19' ),
			'excerpt'  => 'Peaks, valleys and waterfalls in changing autumn weather.',
			'content'  => '<!-- wp:paragraph --><p>Autumn in the mountains changes by the minute. These photographs are made while waiting for gaps in the cloud and the short bursts of light that follow.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'    => 'White Geometry',
			'category' => 'Architecture',
			'type'     => 'Personal project',
			'place'    => 'City',
			'location' => 'Galleries and public buildings',
			'date'     => '2025-09-03',
			'featured' => false,
			'photos'   => array( '20', '10', '26', '11', '27' ),
			'excerpt'  => 'Minimal white architecture, shadows and clean curves.',
			'content'  => '<!-- wp:paragraph --><p>White buildings are really about shadow. This series looks at curves, stairwells and edges, and at how a single direction of light gives them shape.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'    => 'Slow Travel Journal',
			'category' => 'Travel',
			'type'     => 'Travel story',
			'place'    => 'Coast',
			'location' => 'A southern coastline',
			'date'     => '2025-06-19',
			'featured' => true,
			'photos'   => array( '12', '01', '13', '22', '28' ),
			'excerpt'  => 'Villages, narrow streets and coastlines, photographed without hurrying.',
			'content'  => '<!-- wp:paragraph --><p>A travel story told in small scenes: a street at noon, a window, the sea at the end of an alley. Travel work is planned around walking, so the pictures feel like being there rather than passing through.</p><!-- /wp:paragraph -->',
		),
		array(
			'title'    => 'Morning Table',
			'category' => 'Lifestyle',
			'type'     => 'Still life',
			'place'    => 'Studio',
			'location' => 'Studio',
			'date'     => '2025-04-27',
			'featured' => false,
			'photos'   => array( '29', '02', '21', '14', '06' ),
			'excerpt'  => 'Food and everyday objects in soft window light.',
			'content'  => '<!-- wp:paragraph --><p>Still life made with one window and a few simple surfaces. The series is a reminder that good light makes ordinary things worth looking at.</p><!-- /wp:paragraph -->',
		),
	),

	'services' => array(
		array(
			'title'    => 'Portrait Session',
			'photo'    => '21',
			'price'    => 'From EUR 350',
			'duration' => '90 minutes',
			'excerpt'  => 'Natural portraits outdoors or at home, for individuals and families.',
			'content'  => '<p>A relaxed session at a location you choose, planned around the best light of the day.</p>',
			'includes' => array( 'Planning call before the session', 'Up to 90 minutes of photography', '40 edited images in high resolution', 'Private online gallery' ),
		),
		array(
			'title'    => 'Couples and Engagements',
			'photo'    => '15',
			'price'    => 'From EUR 450',
			'duration' => '2 hours',
			'excerpt'  => 'Unhurried photographs of two people, in places that mean something to them.',
			'content'  => '<p>Time to walk, talk and forget the camera. Ideal before a wedding or simply to mark a moment.</p>',
			'includes' => array( 'Location advice', 'Up to 2 hours of photography', '60 edited images', 'Private online gallery' ),
		),
		array(
			'title'    => 'Editorial and Brand',
			'photo'    => '03',
			'price'    => 'Quoted per project',
			'duration' => 'Half or full day',
			'excerpt'  => 'Images for magazines, websites and small brands that need a consistent look.',
			'content'  => '<p>Each brief is different, so the plan, shot list and licence are agreed together before the shoot.</p>',
			'includes' => array( 'Creative brief and shot list', 'Half or full day on location', 'Edited images sized for web and print', 'Usage licence agreed in writing' ),
		),
		array(
			'title'    => 'Interiors and Architecture',
			'photo'    => '23',
			'price'    => 'From EUR 600',
			'duration' => 'Half day',
			'excerpt'  => 'Clean, accurate photographs of homes, studios and buildings.',
			'content'  => '<p>Careful composition and true colour for architects, designers, hosts and property owners.</p>',
			'includes' => array( 'Site visit or video walkthrough', 'Half day of photography', '20 to 30 edited images', 'Vertical lines corrected' ),
		),
		array(
			'title'    => 'Travel and Lifestyle Stories',
			'photo'    => '09',
			'price'    => 'Quoted per project',
			'duration' => '1 to 5 days',
			'excerpt'  => 'Photo stories for hotels, tourism boards and travel writers.',
			'content'  => '<p>A visual story built over several days, with time to wait for the right light.</p>',
			'includes' => array( 'Story planning', 'Multi-day coverage', 'Curated story selection', 'Images delivered in batches' ),
		),
	),

	// General photography advice, so the journal has something real to show
	'posts'    => array(
		array(
			'title'   => 'How to prepare for a portrait session',
			'photo'   => '05',
			'date'    => '2026-09-02',
			'excerpt' => 'A short checklist that makes the day itself easy.',
			'content' => "<!-- wp:paragraph --><p>Most of a good portrait session happens before the camera comes out. Agree the time of day first: an hour after sunrise or an hour before sunset gives soft, directional light that flatters almost everyone.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Pick a place with room to move</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>A place you already like will relax you more than a famous backdrop. Open shade, a long wall, a quiet street corner: all of these work better than a crowded landmark.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Bring one change of clothes</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Two looks are plenty for ninety minutes. Plain colours and simple shapes last longer than prints, which date quickly.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Allow more time than you think</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>The first ten minutes are warming up. The pictures people keep are usually made once everyone has stopped thinking about the camera.</p><!-- /wp:paragraph -->",
		),
		array(
			'title'   => 'Why natural light usually wins',
			'photo'   => '18',
			'date'    => '2026-06-18',
			'excerpt' => 'What you gain, and what you give up, by leaving the lights at home.',
			'content' => "<!-- wp:paragraph --><p>Natural light changes every few minutes, which sounds like a problem and is really an advantage: it gives a session a shape. You start in one kind of light and finish in another, and the pictures tell that story.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Learn one window well</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>A single north-facing window is a complete studio. Stand someone close to it for soft, wrapping light, or further back for contrast and shadow.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">When to add a light</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Interiors at dusk, events indoors and product work on a deadline all deserve a light. Everywhere else, moving the person is faster than moving the equipment.</p><!-- /wp:paragraph -->",
		),
		array(
			'title'   => 'Choosing the photographs that belong in a portfolio',
			'photo'   => '24',
			'date'    => '2026-03-11',
			'excerpt' => 'A portfolio is judged by its weakest picture, not its best one.',
			'content' => "<!-- wp:paragraph --><p>Twenty strong pictures say more than eighty good ones. The quickest improvement most portfolios can make is to remove things, not add them.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Show the work you want again</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>People ask for what they see. If a kind of work is not on the website, it will not arrive in the inbox.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Keep one look across a project</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Colour, contrast and framing that stay consistent within a project make a set feel deliberate. Mixing treatments inside one gallery makes a set feel like a folder.</p><!-- /wp:paragraph -->",
		),
		array(
			'title'   => 'What to wear in front of a camera',
			'photo'   => '11',
			'date'    => '2025-11-24',
			'excerpt' => 'Simple rules that work for portraits, couples and families.',
			'content' => "<!-- wp:paragraph --><p>Clothes should let people look at the face first. That usually means plain fabrics, few logos and a palette that agrees with the place.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Agree a palette for groups</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Three or four tones that sit together are enough. Matching exactly looks staged, clashing pulls the eye away from everyone.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Comfort shows</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Anything that has to be adjusted every few minutes ends up in the pictures. Wear what you would be happy walking a mile in.</p><!-- /wp:paragraph -->",
		),
	),

	'pages'    => array(
		'home'           => array( 'title' => 'Home', 'content' => '' ),
		'about'          => array(
			'title'   => 'About',
			'photo'   => '16',
			'content' => "<!-- wp:paragraph {\"className\":\"is-lead\"} --><p class=\"is-lead\">A photographer working with people, places and light. The pictures are calm, honest and made without rushing.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Philosophy</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Good photographs come from patience. The studio prefers to spend time with the people and places it photographs, waiting for natural light and real moments instead of forcing a pose.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Experience</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>The work covers portraits, lifestyle sessions at home, travel stories, landscapes and architecture. Each kind of work teaches something to the others: architecture brings precision to portraits, and portraits bring warmth to places.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Working together</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Every project starts with a conversation about what you need and how the pictures will be used. From there comes a plan for the location, the timing and the light, so the day itself can feel easy.</p><!-- /wp:paragraph -->\n<!-- wp:paragraph --><p><em>Sample text. Replace it with your own story before the website goes live.</em></p><!-- /wp:paragraph -->",
		),
		'services'       => array(
			'title'   => 'Services',
			'content' => '<!-- wp:paragraph --><p>Every session is planned around light, place and the people in it. The prices below are a starting point, and every booking begins with a short conversation.</p><!-- /wp:paragraph -->',
		),
		'contact'        => array(
			'title'   => 'Contact',
			'content' => '<!-- wp:paragraph --><p>Tell us a little about what you have in mind, and we will reply within two working days with availability and next steps.</p><!-- /wp:paragraph -->',
		),
		'privacy-policy' => array(
			'title'   => 'Privacy Policy',
			'content' => "<!-- wp:paragraph --><p><em>Sample text. Review this policy with a legal adviser before the site goes live.</em></p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Who we are</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>This website belongs to %SITE%. You can contact us through the contact page.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">What we collect</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>When you send the contact form we receive your name, email address, the type of session you chose, an optional preferred date and your message. We use these details only to reply to you and to plan a booking you ask for.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">How long we keep it</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Messages are kept for up to two years, or longer if they become part of a booking and we need them for our records. You can ask us to delete your message at any time.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Cookies</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>The website does not use advertising or tracking cookies. WordPress may set technical cookies when you log in.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Your rights</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>You can ask to see, correct or delete the personal data we hold about you by contacting us.</p><!-- /wp:paragraph -->",
		),
		'terms'          => array(
			'title'   => 'Terms and Conditions',
			'content' => "<!-- wp:paragraph --><p><em>Sample text. Review these terms with a legal adviser before the site goes live.</em></p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Bookings</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>A booking is confirmed once both sides have agreed the details in writing and any deposit has been paid.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Copyright</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>All photographs on this website are protected by copyright. Please do not copy or reuse them without written permission.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Image use</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Clients receive a personal licence for the images from their session. Commercial use is agreed separately for each project.</p><!-- /wp:paragraph -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Changes and cancellations</h2><!-- /wp:heading -->\n<!-- wp:paragraph --><p>Sessions can be moved once without charge with at least seven days notice. Cancellation terms are set out in each booking agreement.</p><!-- /wp:paragraph -->",
		),
	),

	'menus'    => array(
		'primary' => array( 'portfolio', 'about', 'services', 'journal', 'contact' ),
		'footer'  => array( 'privacy-policy', 'terms' ),
	),
);
