<?php include 'header.php';?>
<section>
<h2>First: Extract Images</h2>
<p>Nearly all of the existing algorithms that we want to start applying to our data are built to accept still images rather than moving images. Therefore, the very first logical step was to extract still images from our movie files. Fortunately, each episode was already stored as it's own VOB, or Video Object, file. So we just need a way of converting a VOB file to a set of image files such as JPEG and PNG.</p>
<p>Typically my first step in trying to do something relatively simple like converting between formats starts with a quick web search. Unfortunately in this case, my task is something of interest outside of the data and computer science community. As illustrated by the first few results, the search is dominated by free online converters and one-off, closed source applications:</p>
<div class="figure">
<img src="img/img01.jpg" alt="search results" />
</div>
<p>Scrolling through the first several pages or tweaking the search terms did not seem to find a programatic solution that would work inside of an open-source pipeline. My next idea was to figure out how to do this from within one of the handfull of command line programs that handle multimedia formats. I started by downloading the VLC media player, commonly identified by its orange traffic cone logo. This has turned out to be a great program for seamlessly playing the VOB input files on my laptop, but extracting images was no simple task.</p>
<p>When searching around to figure out how to extract images using VLC, or any other task with VLC as far as I can tell, is greatly complicated by two factors. First of all, my VLC version is 2.2.4 but the majority of third-party tutorials were built for the VLC 1.x and VLC 0.x series. Nearly all of the options and commands seem to have changed over each series and I could not get even the most basic commands to run on my version. Secondly, most users of VLC seem to want instructions on how to use the GUI version of VLC. While there is a command line program, I found nearly no documentation for using it. Even running &quot;VLC --help&quot; provided no information.</p>
<p>Feeling a bit frustrated on how difficult this first task was proving, I went to the only other media processing program I was familiar with: ffmpeg. This is a fairly low-level command line tool that has been continuously developed for the past 16 years. I knew the program primarily through two people that I worked closely with AT&amp;T Labs, both of whom were adamant linux commandline users, who used it as a general-purpose media player. Getting ffmpeg installed on macOS proved to be a challenge in itself. As with any library, my first go-to was to try to install via homebrew (their tag line could not be more accurate: &quot;The missing package manager for macOS&quot;). Unfortunately this produced an entire terminal of error messages. After manually installing several other homebrew formulas, searching down particular errors one by one, and even trying to compile from source (even more errors!), I had had enough. I frustratingly went back to VLC to try to get at least some images through the GUI interface. When even that failed I finally gave up, turned off my machine and went to bed...</p>
<p>The next morning I decided to give installing ffmpeg another shot. Wanting to ensure that everything was as clean as possible, I upgraded homebrew and cleared all of the web caches with <code>brew cleanup</code>. Amazingly, <code>brew install ffmpeg</code> worked on the first try. Opening a terminal, I quickly pulled up the help page for ffmpeg; finally, a proper man page!</p>
<div class="figure">
<img src="img/img02.jpg" alt="ffmpeg man page" />
</div>
<p>Only about 10 minutes of reading the manual yielding this gem of a one-liner:</p>
<div class="sourceCode"><pre class="sourceCode sh"><code class="sourceCode bash"><span class="ex">ffmpeg</span> -i input.VOB -vf fps=1 img/out%04d.png</code></pre></div>
<p>And in less than three minutes I had 1524 still images, one for each second of the episode, extracted into a directory on my desktop. I have never been this excited to see Elizabeth Montgomery's face:</p>
<div class="figure">
<img src="img/img03.png" alt="frame 1" />
</div>
<div class="figure">
<img src="img/img04.png" alt="frame 2" />
</div>
<p>With these images in hand we could finally start testing and modifying our set of image processing libraries.</p>
</section>
<?php include 'footer.php';?>
