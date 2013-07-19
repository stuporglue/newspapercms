<?php

$page->title = "Newspaper Page Tools Help";

$newbody = "<h3>Navigation and Download Tools</h3>";
$newbody .= "<p>The first row of tools helps you navigate the newspapers. These buttons should work in any browser. The page-change drop down requires JavaScript to be enabled.</p>
    <p>Buttons will only appear if applicable to the current page</p>";

$newbody .= "<table><tr><th>Button</th><th>Meaning</th></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/first.png' alt='First page of this issue' title='Previous page of this issue'/></td><td>Go to the first page of the current issue or film.</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/prev.png' alt='Previous page of this newspaper' title='Previous page of this newspaper'/></td><td>Go to the previous page of this newspaper or film. If you are on the first page of the current issue or film, this will take you to the last page of the previous issue.</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/download.png' alt='Download this page' title='Download this page'/></td><td>Download the large version of this page. For speed of browsing, medium sized versions of the pages are shown in your browser. This button will allow you to download the full version for personal use, or for a closer look.</td></tr>
<tr><td class='allpages button'><select title='Select a page to go to' alt='Select a page to go to'>
    <option>1</option><option>2</option><option>3</option>
</select></td><td>This drop down will take you to any page in the current issue or film. This requires a JavaScript enabled browser.</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/next.png' alt='Next page of this newspaper' title='Next page of this newspaper'/></td><td>Go to the next page of the this nespaper. If you are on the last page of the current issue or film, this will take you to the first page of the next issue or film.</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/last.png' alt='Last page of this issue' title='Last page of this issue'/></td><td>Go to the last page of this issue or film.</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/help.png' alt='Help Me!' title='Help Me!'/><td>Brings you to this help page</td></tr>
</table>";

$newbody .= "<h3>Image Editing tools</h3>";
$newbody .= "<p>The image editing tools allow you to temporarily change the image to make it easier to read. The changes only happen in your browser, are not saved, and do not affect anyone else.</p>";
$newbody .= "<p>These tools require an up-to-date browser with support for the HTML5 Canvas to work. Internet Explorer 9+, Firefox 2+, Opera 9.5+, Safari or Chrome should work.</p>";
$newbody .= "<table><tr><th>Button</th><th>Meaning</th></tr>";

$newbody .= "
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/reset.png' alt='Reset' title='Reset'/></td><td>Reset the image</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/rotatec.png' alt='Rotate Right 90 degrees' title='Rotate Right 90 degrees'/></td><td>Rotate Clockwise 90 degrees</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/rotatecc.png' alt='Rotate Left 90 degrees' title='Rotate Left 90 degrees'/></td><td>Rotate Counterclockwise 90 degrees</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/zoomin.png' alt='Zoom In' title='Zoom In'/></td><td>Zoom In</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/zoomout.png' alt='Zoom Out' title='Zoom Out'/></td><td>Zoom Out</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/mirror.png' alt='View the horizontal mirror image' title='View the horizontal mirror image'/></td><td>Mirror image horizontally</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/invert.png' alt='Invert colors' title='Invert colors'/></td><td>Invert colors</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/brighter.png' alt='Lighten the image' title='Ligten the image'/></td><td>Ligten the image</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/darker.png' alt='Darken the image' title='Darken the image'/></td><td>Darken the image</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/morecontrast.png' alt='Increase contrast' title='Increase contrast'/></td><td>Increase contrast</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/lesscontrast.png' alt='Decrease contrast' title='Decrease contrast'/></td><td>Decrease contrast</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/denoise.png' alt='Denoise the image' title='Denoise the image'/></td><td>Denoise the image</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/unsharpmask.png' alt='Sharpen the image with an unsharp mask' title='Sharpen the image with the unsharp mask'/></td><td>Sharpen the image with the unsharp mask</td></tr>
<tr><td class='button'><img src='" . NPC_BASE_URL . "img/busy.gif' alt='An image operation is in progress' title='An image operation is in progress'/></td><td>An image operation is in progress</td></tr>
</table>
";

$page->body = $newbody;