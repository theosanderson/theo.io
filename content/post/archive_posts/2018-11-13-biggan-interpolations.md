---
title: BigGAN interpolations
author: theo
type: post
date: 2018-11-13T14:51:57+00:00
url: /blog/2018/11/13/biggan-interpolations/
categories:
  - Uncategorized

---
The state of the art in image generation is [BigGAN][1].

Now, some [trained models][2] have been made available, including the capacity to interpolate between classes. I made [a colab][3] to easily create animations from these.

They are pretty fun.

<img class="aligncenter size-full wp-image-263" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/cat.gif" alt="" width="256" height="256" /> 

What is more, they make it clear that the latent space clearly captures very meaningful shared properties across classes. The poses of quite different animals are conserved, and &#8220;cat eyes&#8221; clearly map onto &#8220;dog eyes&#8221; during interpolation. These sort of properties suggest that the network &#8216;understands&#8217; the scene it is generating.

<!--more-->

Here are some more:

<img class="aligncenter size-full wp-image-278" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/cattohusky.gif" alt="" width="256" height="256" /> 

<img class="aligncenter size-full wp-image-280" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/leopard.gif" alt="" width="256" height="256" /> 

<img class="aligncenter size-large wp-image-264" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/goose2.gif" alt="" width="256" height="256" /> 

<img class="aligncenter size-large wp-image-267" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/dog.gif" alt="" width="256" height="256" /> 

<img class="aligncenter" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/panda2.gif" alt="" /> 

<img class="aligncenter size-large wp-image-267" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/tiger.gif" alt="" /> 

(this one moves in latent space as well as class space, hence the change of pose:)  
<img class="aligncenter size-large wp-image-267" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/bulldog.gif" alt="" />  
Churches to mosques:

<center>
  <img src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/mosque2.gif" alt="" width="256" height="256" /> <img src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/11/mosque.gif" alt="" width="256" height="256" />
</center>

<center>
</center>

<center>
</center>

<center>
</center>

 [1]: https://arxiv.org/abs/1809.11096
 [2]: https://colab.research.google.com/github/tensorflow/hub/blob/master/examples/colab/biggan_generation_with_tf_hub.ipynb
 [3]: https://colab.research.google.com/drive/1MhfEAOBwhGu1A-F2NSVxGQrkJ4vk7w4V#scrollTo=dSAyfDfnVugs&forceEdit=true&offline=true&sandboxMode=true