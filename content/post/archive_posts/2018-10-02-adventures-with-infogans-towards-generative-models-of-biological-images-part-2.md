---
title: 'Adventures with InfoGANs: towards generative models of biological images (part 2)'
author: theo
type: post
date: 2018-10-02T10:05:06+00:00
url: /blog/2018/10/02/adventures-with-infogans-towards-generative-models-of-biological-images-part-2/
categories:
  - Uncategorized

---
In [the last post][1] I introduced neural networks, generative adversarial networks (GANs) and InfoGANs.

In this post I&#8217;ll describe the motivation and strategy for creating a GAN which generates images of biological cells, like this:  
<!--more-->

  


**Motivation**  
Two microscopic of cells from an identical cell-line are never going to be pixel-for-pixel identical, yet they still share key similarities in terms of morphology. One way to get a computer to demonstrate it &#8220;understands&#8221; these morphological properties is to force it to create images of new cells. When a model is capable of generating any image that might be taken of a specific cell one might argue that it has gained some knowledge about the cell.

This is an appealing approach as it is is easy to acquire large numbers of static images of cells, for instance by using an imaging flow cytometer. This device flows cells past a camera and acquires thousands of images each second, each with a large number of fluorescent channels. These are static images of course, but if they come from an synchronous population they should contain representatives of every stage in the cell cycle. If our generative model was sufficiently capable of truly understanding the structure in the data (and there is [evidence that such models do][2]) then provided if we could generate a model where one dimension in our generator corresponded to pseudotime we could generate timecourse &#8220;videos&#8221; from models trained on these single images. This would, apart from anything else, have practical utility, avoiding the problems of photobleaching in imaging. 

**What I did**

From my work at the [Rayner lab][3] at the Sanger Institute I have access to a large dataset of images of infected red blood cells acquired with an imaging flow cytometer. These images contain both a brightfield transmitted light channel and a fluorescent channel showing DNA in the parasite. I used some filtering (with a classification network) to isolate only images containing single cells which were fully visible. I then trained an InfoGAN in much the same way as in the digit dataset described in the last post.

I tried a couple of different versions, one with a large number of &#8220;communicated&#8221; values &#8211; which is what you see above. 

The network learns about quite a few aspects of _Plasmodium_  
biology, and also some basic optics:

  * It learns about red blood cell morphology and to produce images of plausible cells
  * It learns that the nucleus in the DAPI channel is always within the bounds of the RBC in the brightfield channel.
  * It learns that there are a subset of cells (schizonts) which have both black haemozoin, and widespread, bright DAPI nuclei (often arranged in a circular shape)
  * It learns that cells can appear in front of or behind the focal plane and how to render both types.
  * I could go on.. the images produced by the network are almost all entirely plausible images that a biologist would be unable to distinguish from true parasites.

As an aside I really like watching this network cycle between samples, it is strikingly similar to watching a motile cell wriggling:  
<img src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/09/cell_gan.gif" alt="" width="480" height="160" class="alignnone size-full wp-image-215" /> 

I also trained a [second version][4], with many fewer communicated variables, which really lets us see what the network sees as the most salient features. Unsurprisingly, these are mostly about the location and orientation of the red blood cell. Interestingly this network makes one of the parameters the focal position of the cell:

<img src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/09/focus.gif" alt="" width="480" height="232" class="alignnone size-full wp-image-216" /> 

For any cell you look at, one end of this variable produces one type of halo, and the other the opposite, just as if this parameter was controlling the focus of a microscope.

This property illustrates the sort of property it would be really exciting to get the network to learn. In an ideal world the sliders would represent something like: focus, cell orientation and parasite lifecycle stage. So if you had a young ring stage parasite and you wanted to watch it mature you could just drag the slider across. Or if you wanted to focus on the far side of the cell you could just drag another slider. Such an idea may sound fanciful, but I don&#8217;t think it&#8217;s too far away.

There are a number of simple ways I could improve the work I&#8217;ve done here. The network has to capture the complete distribution of cellular images presented as &#8220;real&#8221;. In this case you can see that these are in many different orientations, and different positions in the image. So a vast amount of the networks efoort goes into recapturing that, not very interesting, spatial variation. I made some basic attempts to align the images presented to the network, but this is something I could definitely improve. Something you will see as the cells dance from one space in the network to another is that while the nucleus tends to move smoothly around the black haemozoin will often fade out in one location and appear in another. The lack of smoothness here is an example of &#8220;modal collapse&#8221; that often haunts GANs, but there are a number of ways to tackle it.

In short, this work just scratches the surface of what what I suspect is possible with generative models of biological cells. 

In some organisms there are genome-wide fluorescent-tag libraries available. Building a generative model using these (possibly with the need for some pairwise imaging) could allow the creation of a synthetic cell in which every protein can be simultaneously visualised. It&#8217;s an exciting prospect, and I think it&#8217;s nearer than it seems.

<small>P.S. I belatedly looked for similar published work, and found two <a href="https://www.biorxiv.org/content/early/2017/12/02/227645">cool</a> <a href="https://arxiv.org/pdf/1708.04692.pdf">papers</a>. The second of these introduces a star-shaped network designed to allow alignment in much the way I imagine at the end of this post. And more generally there are a ton of GAN papers applying the technique to super-resolution microscopy, in silico staining, etc.</small>

 [1]: http://theo.io/blog/2018/10/02/adventures-with-infogans-towards-generative-models-of-biological-images/
 [2]: https://twitter.com/ericjang11/status/1046124500369047553
 [3]: https://www.sanger.ac.uk/science/groups/rayner-group
 [4]: http://theo.io/GAN/plasmodium_gan2/test.html