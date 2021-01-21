---
title: COVID-19 and the categorical imperative
author: theo
type: post
date: 2020-04-21T13:26:41+00:00
url: /blog/2020/04/21/covid-19-and-the-categorical-imperative/
categories:
  - COVID

---
I recently asked on Twitter:<figure class="wp-block-image is-resized">


<img style="border:1px solid #DDD" src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image.png" /> </figure> 

And got reassuring results:<figure class="wp-block-image is-resized">

<img style="border:1px solid #DDD" src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-1.png" alt="" class="wp-image-377" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-1.png 476w, /post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-1-300x67.png 300w" sizes="(max-width: 476px) 85vw, 476px" /> </figure> 

Then I asked another question. Have a think about how you might answer it.<figure class="wp-block-image is-resized">

<img style="border:1px solid #DDD" src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-2.png" alt="" class="wp-image-378" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-2.png 475w, /post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-2-300x171.png 300w" sizes="(max-width: 475px) 85vw, 475px" /> </figure> 

Here is how Twitter responded:<figure class="wp-block-image">

<img style="border:1px solid #DDD" src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-4.png" alt="" class="wp-image-381" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-4.png 425w, /post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-4-300x62.png 300w" sizes="(max-width: 425px) 85vw, 425px" /> </figure> 

Quite a difference! Not surprising perhaps. The risk of passing on COVID-19 is lower by a factor of 12,000. Nevertheless, I think that the good people of Twitter are wrong, and will explain why a little further down.

The people of Twitter are not alone though, and these sorts of decisions aren&#8217;t just thought experiments. The UK government first required people with coughs or fever to isolate themselves on 12 March, long after the virus arrived in the UK. This was part of the government&#8217;s strategy of introducing &#8220;the right measures at the right time&#8221;, and Sir Patrick Vallance gave this as an example, saying that prior to 12 March a very small proportion of people with symptoms would have COVID-19, so it would not make sense to isolate them. 

I&#8217;ve seen a similar argument made against mask use. After lockdown a small proportion of people will have COVID, the argument goes, so the chance of any particular mask stopping an infection is tiny:<figure class="wp-block-image">

<img src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-5.png" alt="" class="wp-image-385" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-5.png 477w, /post/archive_posts/old_wp_images/wp-content/uploads/2020/04/image-5-300x126.png 300w" sizes="(max-width: 477px) 85vw, 477px" /> </figure> 

We do this sort of risk analysis all the time. When you go somewhere in a car there is some small chance you could cause an accident in which someone is hurt, but if that risk were 12,000 times higher you would never drive again.

Even some experts appear to agree:<figure class="wp-block-image is-resized">

<img src="https://pbs.twimg.com/media/EWHRh0OXQAAnd01?format=jpg&name=large" alt="Image" width="426" height="292" /> <figcaption>From [The Times, 21st April][1]</figcaption></figure> 

But this argument does not apply for a contagious virus.

### Investigating with a simple model

Practically, someone who stays home in Scenario A (when there is a 99.999% chance they have a cold), has the same impact as someone who stays home when there is a 12% chance they have COVID. One way to see this is to simulate an epidemic, and imagine that we institute an intervention for only a week, either early in the epidemic or late in the epidemic. 

Let&#8217;s consider the mass-masking scenario. Without any interventions, each infected person passes the virus on to 2.5 additional people. We&#8217;ll exaggerate the effect of masks and imagine that everyone wearing masks reduces that number (R) to 0.8. In reality this magnitude of effect could only be achieved with a package of many interventions. But it makes it easier to see what&#8217;s going on with a graph.

If we plot the epidemic with a logarithmic y-axis, then the steepness of the line is a reflection of how many people each infected person passes the virus to. So if we don&#8217;t implement any interventions, this graph goes up with a constant slope.<figure class="wp-block-image">

<img src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/step1.gif" alt="" class="wp-image-396" /> </figure> 

What happens if we wait until lots of people have the virus, and then bring in masks for 2 weeks to try to stop them passing it on? Well, it&#8217;s as you&#8217;d expect. The graph goes up as normal, then goes down as R falls to 0.8 for 2 weeks, then starts going up at the same rate again, but reaches a lower point after 80 days than without the two week intervention.<figure class="wp-block-image">

<img src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/step2.gif" alt="" class="wp-image-397" /> </figure> 

In this case we decided to bring in the 2 week intervention only after at least 1% of people were infected. This seems to make sense. Surely there is no point in making people wear masks when only 0.001% of them have the virus? But let&#8217;s check, what happens if we bring in the two weeks much earlier in the epidemic?<figure class="wp-block-image">

<img src="/post/archive_posts/old_wp_images/wp-content/uploads/2020/04/step3.gif" alt="" class="wp-image-398" /> </figure> 

It turns out that the two weeks early in the epidemic has exactly the same effect on the number of infections on day 80! What matters is that it has the same effect on R in either case. Even though a far lower absolute number of infected people are wearing masks, R is reduced to 0.8 in both cases.



### Ethics: how should we each act in the time of COVID-19?

As COVID-19 was spreading in our communities, many people advocated for social-distancing measures. One model for doing this is to think about the absolute chance you have of spreading the virus. For example, someone made a graph to discourage people from holding large events by showing that if there are a lot of infections in the community there is a good chance that large events will cause transmission events:<figure class="wp-block-image">

![Image][2] </figure> 

Interestingly this graph suggests that if one holds a large event when there is a low level of COVID in the community, there is a less negative effect than when there is a high level. But we&#8217;ve seen from the models above that this isn&#8217;t correct.

So how can we decide how to shape our behaviour? I think it makes sense to follow Kant&#8217;s &#8220;[Categorical Imperative][3]&#8220;.

<blockquote class="wp-block-quote">
  <p>
    <strong>Act only according to that&nbsp;maxim&nbsp;whereby you can, at the same time, will that it should become a universal law.</strong>
  </p>
</blockquote>

So imagine if everyone acted as you intend to, and work out what the effect on R would be. If everyone wears masks then, regardless of the level of COVID in the community, R is reduced. If everyone holds large events then, regardless of the level of COVID in the community, R is increased. 

In the absence of evidence on seroprevalence and immunity, we must aim to keep R below 1 until we have a vaccine. This will drive the level in the community down to near zero. It will allow vulnerable people to go about their lives as normal. However to achieve these levels we must sustain our measures _even as levels in the community drop_. Even as we believe that only 30 people in the whole country are infected, we must all wear masks. It&#8217;s counter-intuitive, but it will work.

 [1]: https://www.thetimes.co.uk/article/facemasks-for-public-risk-nhs-shortage-q5bkzvzql
 [2]: https://pbs.twimg.com/media/EV5HbwjVAAYZbz0?format=jpg&name=4096x4096
 [3]: https://en.wikipedia.org/wiki/Categorical_imperative