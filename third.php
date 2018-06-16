<?php include 'header.php';?>

      <section>
<h2>Third: Shot Detection</h2>
<p>The testing with face detection showed a need to incorporate scene detection into our algorithm as well. There is a relatively low recall but high precision, which we can use to combine over still images in a given seen to detect where characters are. I found a number of libraries that attempt to find shot or scene breaks, including <a href="https://github.com/Breakthrough/PySceneDetect">PySceneDetect</a> and <a href="https://github.com/jgbutler/Shot-Logger">ShotLogger</a>. In testing, both seemed too high-level for our needs. Testing the python library directly on our movie file found only a small subset of the total available shot breaks (dispite the name, their software is actually looking for shot breaks rather than scene breaks). The ShotLogger software has great results on its website, but I was not able to get it running on my own in a reasonable amount of time. At any rate, it will be very useful to write our own scene detector.</p>
<p>Ideally, I want a function that takes two images and compares how 'close' they are to one another. A shot break can then be classified as an abrubt change in this metric between adjacent frames. This metric may then also be useful in telling us when the actual scene has ended or when a camera angle has been returned too from a previous break. As a starting point, I converted each frame to a 100x100 pixel image and then compared two images using Euclidiean distance in the HSV color space. I used HSV coordinates; I have found in many applications that it works better than the standard RGB coordinate system and rarely if ever perform worse. Down-sampling to a smaller grid allows the metric to stay small even when the camera or characters are moving within a shot.</p>
<p>Here is an example of the metric applied to adjacent shots, indexed by the frame number. I added the red dot to show where their is a true shot break.</p>
<div class="figure">
<img src="img/img09.png" alt="image" />
</div>
<p>In this example the algorithm does a good job of highlighting an abnormally large change at the actual scene break. The values around 3695, which are not as large as the shot break, are large because the characters are moving around the set. I manually tested this algorithm over randomly choosen runs of about 150 frames. While most worked well, it did not take long to find an anomalous example. Here is one from a scene at Darrin's office with the three real shot breaks denoted by red dots:</p>
<div class="figure">
<img src="img/img10.png" alt="image" />
</div>
<p>There is no way to use a threshold that captures the real breaks but avoids all of the non-real breakse. What is going on here? I took a look at the transitions with large values at 4139 and 4164. Around both frames we have very fast movement with Larry darting across the scene:</p>
<div class="figure">
<img src="img/img16.png" alt="image" />
</div>
<div class="figure">
<img src="img/img17.png" alt="image" />
</div>
<p>As I thought about what is happening to the Euclidian distance in HSV space, it began to make sense that fast movement could be a problem. I convereted the metric to measure instead the median absolute value of the differences between the images. Because character movement show not change the majority to the 100x100 pixel bins, this should be much more robust to movement. Looking at the results, it certainly helps smooth things out and to clarify where the real scene breaks are:</p>
<div class="figure">
<img src="img/img11.png" alt="image" />
</div>
<p>We still seem to have a problem with frame 4155. Although there is a scene break, the prior image is interlaced with the next so the change is too gradual to detect with a high signal:</p>
<div class="figure">
<img src="img/img15.png" alt="image" />
</div>
<p>At first, I tried changing grid size from 100x100 to 25x25, but this was not particularly helpful (I ultimately kept the 25x25 grid as it helped slightly in other examples):</p>
<div class="figure">
<img src="img/img12.png" alt="image" />
</div>
<p>After searching around on the internet, I learned that the interlacing in the video is a known problem when converting from formats meant for older tvs. There is also a quick way to fix this when calling ffmpeg. Using the deinterlace option, the still images were now de-interlaced:</p>
<div class="sourceCode"><pre class="sourceCode sh"><code class="sourceCode bash"><span class="ex">ffmpeg</span> -i input.VOB -vf fps=6 -deinterlace img/out%06d.png</code></pre></div>
<p>Using these new images along with the median absolute deviation (in truth, I'm using the 40% of the absolute deviations as it seemed to be slightly more stable) fixes the problem in this case. Real transitions are all above non-real transitions:</p>
<div class="figure">
<img src="img/img13.png" alt="image" />
</div>
<p>The difference between the real break and the non-real one in this example, however, is still not very large. It seemed unlikely that this would work for an universal cut-off to denote shot breaks. I needed a way of making the metric less extreme in the presense of movement. To do that, I re-ran ffmpeg once again with the frame rate set to 24 frames per second. With this data, we see a true seperation between the true and false scene breaks (note that the indicies have changes because there are now 4 times as many frames):</p>
<div class="figure">
<img src="img/img14.png" alt="image" />
</div>
<p>In this example there is a clear range of cut-off values that would work (e.g., 0.4) and that seem reasonably accurate for indicating scene breaks. It may appear that we could have just use the higher framerate to begin with and skip all of the other steps. For example, here is the mean difference metric on the deinterlaced 24fps data:</p>
<div class="figure">
<img src="img/img18.png" alt="image" />
</div>
<p>Even at the higher frame rate, the original metric does a poor job of differentiating movement and shot breaks.</p>

      </section>

<?php include 'footer.php';?>
