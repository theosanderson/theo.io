---
title: "New-variant compatibility in the ONS infection survey"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-01-22T14:18:37Z
lastmod: 2021-01-22T14:58:37Z
featured: false
draft: false
projects: []
mininote: true
output:  hugodown::md_document  
rmd_hash: f098c7b91c751bb2

---

<small>Code behind this analysis: <a href="https://github.com/theosanderson/theo.io/tree/master/content/post/2021-01-22-ons-data" class="uri">https://github.com/theosanderson/theo.io/tree/master/content/post/2021-01-22-ons-data</a></small>

The ONS infection survey has come out and there has been a lot of discussion on the apparent decrease in proportion of "new variant compatible" cases.

![](ons.png)

To try to understand these patterns we need to go into a bit more detail. How are "new variant compatible" cases defined?

The TaqPath tests that produce this data amplify parts of three genes in the SARS-CoV2 genome:

-   The N gene
-   ORF1ab
-   The S gene

We know that B.1.1.7 often gives complete loss of the S-gene amplicon (data from Portugal suggest it doesn't [always](https://virological.org/t/tracking-sars-cov-2-voc-202012-01-lineage-b-1-1-7-dissemination-in-portugal-insights-from-nationwide-rt-pcr-spike-gene-drop-out-data/600)).

When B.1.1.7 was emerging the ONS defined a fairly conservative definition of "new variant compatible" tests. They said that these must be positive for N, *and* ORF1AB, but not for S. That is what we would expect to see for B.1.1.7 when there a lot of virions in the sample. But if a sample has a low number of virions, one or other of these genes might randomly drop below the detection threshold. Fortunately the ONS also report the data split out by each of the possible amplicon combinations, so we can examine this.

First let's convince ourselves that, irrespective of B.1.1.7, dropouts of amplicons can occur for various reasons. Let's look at the distribution of amplicons period of time in September, before B.1.1.7 had really emerged.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>subset</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>week</span><span class='o'>==</span><span class='s'>"2020-09-21"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>RegionType</span><span class='o'>==</span><span class='s'>"EnglandRegion"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>AmpliconType <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span>
 <span class='nv'>Amplicons</span> <span class='o'>%in%</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"N only"</span>,<span class='s'>"N+S"</span>,<span class='s'>"OR only"</span>,<span class='s'>"OR+S"</span>,<span class='s'>"S only"</span><span class='o'>)</span> <span class='o'>~</span> <span class='s'>"Random dropout"</span>,
  <span class='nv'>Amplicons</span> <span class='o'>==</span> <span class='s'>"OR+N+S"</span> <span class='o'>~</span> <span class='s'>"Definite non-B.1.1.7"</span>,
  <span class='nv'>Amplicons</span> <span class='o'>==</span> <span class='s'>"OR+N"</span> <span class='o'>~</span> <span class='s'>"Likely(?) B.1.1.7"</span>
<span class='o'>)</span><span class='o'>)</span>
                                                                                                
                                                                                                
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>subset</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>AmpliconType</span>,y<span class='o'>=</span><span class='nv'>Count</span>,fill<span class='o'>=</span><span class='nv'>Amplicons</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_bar</span><span class='o'>(</span>stat<span class='o'>=</span><span class='s'>"identity"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nv'>Region</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span> <span class='nf'>theme</span><span class='o'>(</span>axis.text.x <span class='o'>=</span> <span class='nf'>element_text</span><span class='o'>(</span>angle <span class='o'>=</span> <span class='m'>90</span>, vjust <span class='o'>=</span> <span class='m'>0.5</span>, hjust<span class='o'>=</span><span class='m'>1</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-2-1.png" width="700px" style="display: block; margin: auto;" />

</div>

I've separated out the `OR+N` amplicon types, which the ONS considers new variant compatible. Let's ignore these for now. We can see that there is a substantial variation in the other amplicon groups. If there were no random dropout (and no other variants that compromise an assay), we would expect everything else to be `OR+N+S`. But in the South West these make up a minority of the total positives identified. Instead it seems that some of these amplicons are randomly dropping out there.

We can also see a lot of geographical heterogeneity in the level of this random dropout. What drives this? We also have the mean Ct values for each region so we can test the hypothesis that high Ct values (low number of virions), due to a higher proportion of infections being identified longer after the original infection, are responsible for the "random dropout".

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='c'># Exclude suspected B.1.1.7 from the data</span>
<span class='nv'>subset</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>week</span><span class='o'>==</span><span class='s'>"2020-09-21"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>RegionType</span><span class='o'>==</span><span class='s'>"EnglandRegion"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Amplicons</span><span class='o'>!=</span><span class='s'>"OR+S"</span><span class='o'>)</span>

<span class='c'>#Calculate the proportion with "no random dropout", then invert to get proportion of "random dropout"</span>
<span class='nv'>subset</span> <span class='o'>=</span><span class='nv'>subset</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>Region</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>proportion <span class='o'>=</span> <span class='nv'>Count</span><span class='o'>/</span><span class='nf'><a href='https://rdrr.io/r/base/sum.html'>sum</a></span><span class='o'>(</span><span class='nv'>Count</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Amplicons</span><span class='o'>==</span><span class='s'>"OR+N+S"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>RandomDropout<span class='o'>=</span><span class='m'>1</span><span class='o'>-</span><span class='nv'>proportion</span><span class='o'>)</span>

<span class='nv'>subset_cts</span> <span class='o'>=</span> <span class='nv'>data_ct</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>week</span><span class='o'>==</span><span class='s'>"2020-09-21"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>RegionType</span><span class='o'>==</span><span class='s'>"EnglandRegion"</span><span class='o'>)</span>

<span class='nv'>both</span> <span class='o'>&lt;-</span> <span class='nf'>inner_join</span><span class='o'>(</span><span class='nv'>subset</span>,<span class='nv'>subset_cts</span><span class='o'>)</span> 

<span class='c'>#&gt; Joining, by = c("Region", "RegionType", "week")</span>


<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>both</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Mean</span>,y<span class='o'>=</span><span class='nv'>RandomDropout</span>,label<span class='o'>=</span><span class='nv'>Region</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_point</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Mean Ct value"</span>,y<span class='o'>=</span><span class='s'>"Proportion of 'random dropout'"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'><a href='https://rdrr.io/pkg/ggrepel/man/geom_text_repel.html'>geom_text_repel</a></span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span><span class='s'>"Relationship between Ct and levels of random dropout in September"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-3-1.png" width="700px" style="display: block; margin: auto;" />

</div>

Having convinced ourselves that random dropouts can occur at different levels due to differences in Ct values (and that Ct values [can differ due to epidemiological dynamics](https://www.medrxiv.org/content/10.1101/2020.10.08.20204222v1))), we can examine whether an increased rate of random dropouts in the N or ORF1ab genes (or both!) might cause an underestimation of B.1.1.7 prevalence when considering only `OR+N+S` as compatible with the new variant.

Now lets look at the trajectories of all amplicon groups over time in England.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>Region</span>,<span class='nv'>week</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>proportion <span class='o'>=</span> <span class='nv'>Count</span><span class='o'>/</span><span class='nf'><a href='https://rdrr.io/r/base/sum.html'>sum</a></span><span class='o'>(</span><span class='nv'>Count</span><span class='o'>)</span><span class='o'>)</span> 

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Region</span><span class='o'>==</span><span class='s'>"England"</span><span class='o'>)</span>, <span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>week</span>,y<span class='o'>=</span><span class='nv'>proportion</span>,color<span class='o'>=</span><span class='nv'>Amplicons</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-4-1.png" width="700px" style="display: block; margin: auto;" />

</div>

We can see a rise in `OR+N` over time. But this seems to tail off to at least horizontal at the end. Does this mean that B.1.1.7 is no longer increasing at the expense of other variants? Well I don't think we have evidence for this. `OR+N` may be horizontal, but `OR+N+S` seems likely to be falling faster. It is likely that this is due to a general change in the epidemic stage, as lockdown controls new infections and a higher proportion of old detections are detected.

Again if we want to look for an explanation we can investigate the Cts.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data_ct</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>RegionType</span><span class='o'>==</span><span class='s'>"EnglandRegion"</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>week</span>, y<span class='o'>=</span><span class='nv'>Mean</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>title<span class='o'>=</span><span class='s'>"Regions"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nv'>Region</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>y<span class='o'>=</span><span class='s'>"Mean Ct value"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-5-1.png" width="700px" style="display: block; margin: auto;" />

</div>

In all regions we see an increase in mean Ct over January, which we'd expect to cause more random dropouts, and reduce the number of both WT viruses that appear as `OR+N+S` and of B.1.1.7 viruses that appear as `OR+N`.

One (imperfect) way to try to get a handle on this is to just plot the ratio of `OR+N+S` to `OR+N`, because both of these are affected by random drop out.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Region</span><span class='o'>==</span><span class='s'>"England"</span>,<span class='nv'>Amplicons</span> <span class='o'>%in%</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"OR+N+S"</span>,<span class='s'>"OR+N"</span><span class='o'>)</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>week</span>, fill<span class='o'>=</span><span class='nv'>Amplicons</span>,y<span class='o'>=</span><span class='nv'>Count</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_bar</span><span class='o'>(</span>stat<span class='o'>=</span><span class='s'>"identity"</span>,position<span class='o'>=</span><span class='s'>"fill"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>title<span class='o'>=</span><span class='s'>"England"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-6-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>RegionType</span><span class='o'>==</span><span class='s'>"EnglandRegion"</span>,<span class='nv'>Amplicons</span> <span class='o'>%in%</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"OR+N+S"</span>,<span class='s'>"OR+N"</span><span class='o'>)</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>week</span>, fill<span class='o'>=</span><span class='nv'>Amplicons</span>,y<span class='o'>=</span><span class='nv'>Count</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_bar</span><span class='o'>(</span>stat<span class='o'>=</span><span class='s'>"identity"</span>,position<span class='o'>=</span><span class='s'>"fill"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>title<span class='o'>=</span><span class='s'>"Regions"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nv'>Region</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-6-2.png" width="700px" style="display: block; margin: auto;" />

</div>

Similarly we can also plot how the proportion of B.1.1.7 incompatible cases (those that do show S amplification) has changed.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>B117_incompatible <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>grepl</a></span><span class='o'>(</span><span class='s'>'S'</span>,<span class='nv'>Amplicons</span><span class='o'>)</span><span class='o'>)</span>

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Region</span><span class='o'>==</span><span class='s'>"England"</span>,<span class='nv'>Amplicons</span> <span class='o'>%in%</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"OR+N+S"</span>,<span class='s'>"OR+N"</span><span class='o'>)</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>week</span>, fill<span class='o'>=</span><span class='nv'>B117_incompatible</span>,y<span class='o'>=</span><span class='nv'>Count</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_bar</span><span class='o'>(</span>stat<span class='o'>=</span><span class='s'>"identity"</span>,position<span class='o'>=</span><span class='s'>"fill"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>title<span class='o'>=</span><span class='s'>"England"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-7-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>RegionType</span><span class='o'>==</span><span class='s'>"EnglandRegion"</span>,<span class='nv'>Amplicons</span> <span class='o'>%in%</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"OR+N+S"</span>,<span class='s'>"OR+N"</span><span class='o'>)</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>week</span>, fill<span class='o'>=</span><span class='nv'>B117_incompatible</span>,y<span class='o'>=</span><span class='nv'>Count</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_bar</span><span class='o'>(</span>stat<span class='o'>=</span><span class='s'>"identity"</span>,position<span class='o'>=</span><span class='s'>"fill"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>title<span class='o'>=</span><span class='s'>"Regions"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nv'>Region</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-7-2.png" width="700px" style="display: block; margin: auto;" />

</div>

to be continued

