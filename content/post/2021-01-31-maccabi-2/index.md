---
title: "Looking again at Maccabi data for efficacy after initial Pfizer dose"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-01-31T10:18:37Z
lastmod: 2021-01-31T10:18:37Z
featured: false
draft: false
projects: []
mininote: true
output: 
  hugodown::md_document:
    fig_width: 6 
    fig_asp: 0.59
rmd_hash: 3ab4d85f6f0dbdcf

---

**Update(2021-02-03):** Maccabi have also now released a [different report](https://kinstitute.org.il/wp-content/uploads/2021/02/linkedin-post-28-days-01.02-3-PDF.pdf) looking at a cohort, which gives a less rosy picture of initial efficacy. The explanation may be the older age group in the cohort. I think the calculations below still stand, but am flagging this important further data for context.
<hr>

Maccabi have now released a [preprint](https://www.medrxiv.org/content/10.1101/2021.01.27.21250612v1) on Pfizer vaccine efficacy after the first dose. Let's take a look at it.

Firstly a little complaint. If you go to the \`Data' tab on bioRxiv, it says:

![](data_availability.png)

Now of course one understands that there may be lots of underlying patient data that can't be released for regulatory reasons. But some data definitely can, because they release it in this preprint, in graphical form.

![](maccabi.png)

At bare minimum, we should demand that the data directly plotted in graphs are released in numerical form to permit further analysis. There can be no regulatory justification for not doing so.

Fortunately, the data are there, we just need to extract them. For this image file I did that by hand with [WebPlotDigitizer](https://apps.automeris.io/wpd/).

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>maccabi</span> <span class='o'>=</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='s'>"maccabi.csv"</span>,col_names <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>'Day'</span>,<span class='s'>'CumulativeIncidence'</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>Day<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/Round.html'>round</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>)</span><span class='o'>)</span> 

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>maccabi</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>CumulativeIncidence</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>"blue"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Cumulative Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-2-1.png" width="700px" style="display: block; margin: auto;" />

</div>

Looks like we've pretty much got it. Now let's convert that cumulative incidence into a daily incidence (something not done in the Maccabi preprint).

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>perday</span> <span class='o'>=</span> <span class='nv'>maccabi</span> <span class='o'>%&gt;%</span> <span class='nf'>arrange</span><span class='o'>(</span><span class='nv'>Day</span>,<span class='nv'>CumulativeIncidence</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>summarise</span><span class='o'>(</span>CumulativeIncidence<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>max</a></span><span class='o'>(</span><span class='nv'>CumulativeIncidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>DailyIncidence<span class='o'>=</span><span class='nv'>CumulativeIncidence</span><span class='o'>-</span><span class='nf'><a href='https://rdrr.io/r/stats/lag.html'>lag</a></span><span class='o'>(</span><span class='nv'>CumulativeIncidence</span><span class='o'>)</span>,type<span class='o'>=</span><span class='s'>"Vaccinated"</span><span class='o'>)</span> 

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>perday</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>DailyIncidence</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>"blue"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Daily Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span> 

</code></pre>
<img src="figs/unnamed-chunk-3-1.png" width="700px" style="display: block; margin: auto;" />

</div>

Now we have a fair idea of what went on. On day 1 after vaccination, 0.024% of those vaccinated tested positive. That proportion seems to have risen until about day 8, when 0.06% of those vaccinated tested positive. It then fell until day 25, when just 0.005% tested positive.

Now, to properly calculate vaccine efficacy we would need some control group who have not been vaccinated. In the original trials that was a randomly selected half who were given a placebo injection. We compared how many cases they developed, as compared to those who were vaccinated.

There is no placebo group here. No set of matching people who were not vaccinated. So to have any way of calculating an efficacy value we essentially have to invent one and imagine what the incidence would have been in it.

Doing this for the first \~10 days, seems pretty easy. We really don't expect to see the vaccine have any effect here, so the control group might look something like this:

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>control_group</span> <span class='o'>=</span> <span class='nf'>tibble</span><span class='o'>(</span>Day<span class='o'>=</span><span class='m'>1</span><span class='o'>:</span><span class='m'>10</span>,DailyIncidence<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/seq.html'>seq</a></span><span class='o'>(</span><span class='m'>0.00032</span>,<span class='m'>0.00059</span>,length.out<span class='o'>=</span><span class='m'>10</span><span class='o'>)</span>,type<span class='o'>=</span><span class='s'>"Synthetic control"</span><span class='o'>)</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>control_group</span>, <span class='nv'>perday</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>DailyIncidence</span>,color<span class='o'>=</span><span class='nv'>type</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>""</span>,x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Daily Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>scale_color_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>,<span class='s'>"blue"</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-4-1.png" width="700px" style="display: block; margin: auto;" />

</div>

The reason for this not being a horizontal line would have to be some genuine rise in cases over this period. One possibility is the general increasing number of cases in Israel, which continued up to around 15 Jan. 

Unforunately, having this synthetic control line for the first 7 days isn't especially useful. By construction this shows \~0% efficacy in that period, because that's what we designed it to. What I'm really interested is that the efficacy around Day 24. So how should we extend the line?

I've illustrated 3 possible scenarios below, one where cases continue to rise in the placebo group, another where they stay flat, and another where they fall :

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'> <span class='nv'>syn_control</span> <span class='o'>&lt;-</span> <span class='kr'>function</span><span class='o'>(</span><span class='nv'>final</span>, <span class='nv'>name</span>,<span class='nv'>offset</span><span class='o'>)</span><span class='o'>&#123;</span>
<span class='kr'><a href='https://rdrr.io/r/base/function.html'>return</a></span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nf'>tibble</span><span class='o'>(</span>Day<span class='o'>=</span><span class='m'>1</span><span class='o'>:</span><span class='m'>10</span>,DailyIncidence<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/seq.html'>seq</a></span><span class='o'>(</span><span class='m'>0.00032</span><span class='o'>+</span><span class='nv'>offset</span>,<span class='m'>0.00059</span><span class='o'>+</span><span class='nv'>offset</span>,length.out<span class='o'>=</span><span class='m'>10</span><span class='o'>)</span>,type<span class='o'>=</span><span class='nv'>name</span><span class='o'>)</span>,
                  <span class='nf'>tibble</span><span class='o'>(</span>Day<span class='o'>=</span><span class='m'>10</span><span class='o'>:</span><span class='m'>25</span>,DailyIncidence<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/seq.html'>seq</a></span><span class='o'>(</span><span class='m'>0.00059</span><span class='o'>+</span><span class='nv'>offset</span>,<span class='nv'>final</span>,length.out<span class='o'>=</span><span class='m'>16</span><span class='o'>)</span>,type<span class='o'>=</span><span class='nv'>name</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>)</span>
<span class='o'>&#125;</span>
<span class='nv'>combo</span> <span class='o'>=</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nf'>syn_control</span><span class='o'>(</span><span class='m'>0.0010</span>,<span class='s'>"Synthetic control: Growth"</span>,<span class='m'>0.00001</span><span class='o'>)</span>,<span class='nf'>syn_control</span><span class='o'>(</span><span class='m'>0.00059</span>,<span class='s'>"Synthetic control: flat"</span>,<span class='m'>0</span><span class='o'>)</span>,<span class='nf'>syn_control</span><span class='o'>(</span><span class='m'>0.00029</span>,<span class='s'>"Synthetic control: decreasing"</span>,<span class='o'>-</span><span class='m'>0.00001</span><span class='o'>)</span>, <span class='nv'>perday</span><span class='o'>)</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>combo</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>DailyIncidence</span>,color<span class='o'>=</span><span class='nv'>type</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>""</span>,x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Daily Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>scale_color_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"pink"</span>,<span class='s'>"red"</span>,<span class='s'>"darkred"</span>,<span class='s'>"blue"</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-5-1.png" width="700px" style="display: block; margin: auto;" />

</div>

These choices have a substantial effect on the efficacy we measure for days 23-25, which range from 82% (placebo cases fall) to 94% (placebo cases rise).

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>combo</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>&gt;</span><span class='m'>22</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>type</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>summarise</span><span class='o'>(</span>Incidence <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/mean.html'>mean</a></span><span class='o'>(</span><span class='nv'>DailyIncidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>efficacy_percent <span class='o'>=</span> <span class='m'>100</span><span class='o'>*</span><span class='o'>(</span><span class='m'>1</span><span class='o'>-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>Incidence</span><span class='o'>)</span><span class='o'>/</span><span class='nv'>Incidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>type</span><span class='o'>!=</span><span class='s'>"Vaccinated"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>select</span><span class='o'>(</span><span class='o'>-</span><span class='nv'>Incidence</span><span class='o'>)</span>

<span class='c'>#&gt; `summarise()` ungrouping output (override with `.groups` argument)</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 3 x 2</span></span>
<span class='c'>#&gt;   type                          efficacy_percent</span>
<span class='c'>#&gt;   <span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>                                    </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>1</span><span> Synthetic control: decreasing             82.4</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>2</span><span> Synthetic control: flat                   90.8</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>3</span><span> Synthetic control: Growth                 94.4</span></span>
</code></pre>

</div>

So what did Maccabi do? Well something quite different to any of these. They used the period from days 0 to 12 as the control for days 13 to 24. That looks like this:

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>control_group</span> <span class='o'>=</span> <span class='nv'>perday</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>Day<span class='o'>=</span><span class='nv'>Day</span><span class='o'>+</span><span class='m'>12</span>, type<span class='o'>=</span> <span class='s'>"Synthetic control"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>&lt;</span><span class='m'>25</span><span class='o'>)</span>
<span class='nv'>maccabi_analysis</span> <span class='o'>=</span> <span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>control_group</span>, <span class='nv'>perday</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>&lt;</span><span class='m'>25</span><span class='o'>)</span><span class='o'>)</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>maccabi_analysis</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>DailyIncidence</span>,color<span class='o'>=</span><span class='nv'>type</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>""</span>,x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Daily Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>scale_color_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>,<span class='s'>"blue"</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-7-1.png" width="700px" style="display: block; margin: auto;" />

</div>

If again you look at day 23-24 efficacy here, you get an efficacy of 88%.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>maccabi_analysis</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>&gt;</span><span class='m'>22</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>type</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>summarise</span><span class='o'>(</span>Incidence <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/mean.html'>mean</a></span><span class='o'>(</span><span class='nv'>DailyIncidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>efficacy_percent <span class='o'>=</span> <span class='m'>100</span><span class='o'>*</span><span class='o'>(</span><span class='m'>1</span><span class='o'>-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>Incidence</span><span class='o'>)</span><span class='o'>/</span><span class='nv'>Incidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>type</span><span class='o'>!=</span><span class='s'>"Vaccinated"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>select</span><span class='o'>(</span><span class='o'>-</span><span class='nv'>Incidence</span><span class='o'>)</span>

<span class='c'>#&gt; `summarise()` ungrouping output (override with `.groups` argument)</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 1 x 2</span></span>
<span class='c'>#&gt;   type              efficacy_percent</span>
<span class='c'>#&gt;   <span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>                        </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>1</span><span> Synthetic control             87.5</span></span>
</code></pre>

</div>

Maccabi however looked across the entire period. If you do that you get an efficacy close to 50% (they got 51%, I got 56%, possibly due to slight differences in the weighting of different days based on the number of people in them).

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>maccabi_analysis</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>&gt;</span><span class='m'>12</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>type</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>summarise</span><span class='o'>(</span>Incidence <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/mean.html'>mean</a></span><span class='o'>(</span><span class='nv'>DailyIncidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>efficacy_percent <span class='o'>=</span> <span class='m'>100</span><span class='o'>*</span><span class='o'>(</span><span class='m'>1</span><span class='o'>-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>Incidence</span><span class='o'>)</span><span class='o'>/</span><span class='nv'>Incidence</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>type</span><span class='o'>!=</span><span class='s'>"Vaccinated"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>select</span><span class='o'>(</span><span class='o'>-</span><span class='nv'>Incidence</span><span class='o'>)</span>

<span class='c'>#&gt; `summarise()` ungrouping output (override with `.groups` argument)</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 1 x 2</span></span>
<span class='c'>#&gt;   type              efficacy_percent</span>
<span class='c'>#&gt;   <span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>                        </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>1</span><span> Synthetic control             56.1</span></span>
</code></pre>

</div>

So, what to conclude? Essentially the way Maccabi analysed this data was one relatively arbitrary possibility among many. Efficacy estimates are quite sensitive to the guesses one makes about how many cases there would have been in the control group. Maccabi's decision here is quite conservative in that it includes as a control group the day 10 to 12 period where some cases may well have been averted by vaccination.

The decision to look at the entire day 12-24 period rather than the end of it, will yield a result lower than the true efficacy towards the end of this period, given the clear evidence for an increase in efficacy over the period. This is incredibly valuable data, and all in all, things look pretty good.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>perday</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>DailyIncidence</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>"black"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Daily Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_smooth</span><span class='o'>(</span><span class='o'>)</span>

<span class='c'>#&gt; `geom_smooth()` using method = 'loess' and formula 'y ~ x'</span>

</code></pre>
<img src="figs/unnamed-chunk-10-1.png" width="700px" style="display: block; margin: auto;" />

</div>

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>perday</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 26 x 4</span></span>
<span class='c'>#&gt;      Day CumulativeIncidence DailyIncidence type      </span>
<span class='c'>#&gt;    <span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span><span>               </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span><span>          </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span><span> </span><span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>     </span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 1</span><span>     0           0.000</span><span style='text-decoration: underline;'>025</span><span>1      </span><span style='color: #BB0000;'>NA</span><span>        Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 2</span><span>     1           0.000</span><span style='text-decoration: underline;'>269</span><span>        0.000</span><span style='text-decoration: underline;'>244</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 3</span><span>     2           0.000</span><span style='text-decoration: underline;'>626</span><span>        0.000</span><span style='text-decoration: underline;'>357</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 4</span><span>     3           0.000</span><span style='text-decoration: underline;'>990</span><span>        0.000</span><span style='text-decoration: underline;'>363</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 5</span><span>     4           0.001</span><span style='text-decoration: underline;'>49</span><span>         0.000</span><span style='text-decoration: underline;'>501</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 6</span><span>     5           0.001</span><span style='text-decoration: underline;'>97</span><span>         0.000</span><span style='text-decoration: underline;'>476</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 7</span><span>     6           0.002</span><span style='text-decoration: underline;'>47</span><span>         0.000</span><span style='text-decoration: underline;'>501</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 8</span><span>     7           0.003</span><span style='text-decoration: underline;'>03</span><span>         0.000</span><span style='text-decoration: underline;'>564</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 9</span><span>     8           0.003</span><span style='text-decoration: underline;'>65</span><span>         0.000</span><span style='text-decoration: underline;'>614</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>10</span><span>     9           0.004</span><span style='text-decoration: underline;'>13</span><span>         0.000</span><span style='text-decoration: underline;'>489</span><span> Vaccinated</span></span>
<span class='c'>#&gt; <span style='color: #555555;'># … with 16 more rows</span></span>m


<span class='nv'>incidence_data</span> <span class='o'>=</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='s'>"data_from_chart.csv"</span><span class='o'>)</span> 

<span class='c'>#&gt; Parsed with column specification:</span>
<span class='c'>#&gt; cols(</span>
<span class='c'>#&gt;   Day = <span style='color: #00BB00;'>col_double()</span><span>,</span></span>
<span class='c'>#&gt;   VaccinatedCohort = <span style='color: #00BB00;'>col_double()</span></span>
<span class='c'>#&gt; )</span>

<span class='nv'>cohort_size</span><span class='o'>=</span><span class='m'>132015</span>
<span class='nv'>incidence_data</span><span class='o'>$</span><span class='nv'>CumulativeIncidence</span> <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/cumsum.html'>cumsum</a></span><span class='o'>(</span><span class='nv'>incidence_data</span><span class='o'>$</span><span class='nv'>VaccinatedCohort</span><span class='o'>)</span><span class='o'>/</span> <span class='nv'>cohort_size</span>
<span class='nv'>incidence_data</span><span class='o'>$</span><span class='nv'>type</span> <span class='o'>=</span> <span class='s'>"Incidence plot"</span>



<span class='nv'>maccabi_offset</span> <span class='o'>=</span> <span class='nv'>maccabi</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>&gt;</span><span class='m'>3</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>CumulativeIncidence <span class='o'>=</span> <span class='nv'>CumulativeIncidence</span><span class='o'>-</span><span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>CumulativeIncidence</span><span class='o'>)</span>, type<span class='o'>=</span><span class='s'>"Kaplan-Meier plot"</span> <span class='o'>)</span>
 


<span class='nf'>ggplot</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>incidence_data</span>,<span class='nv'>maccabi_offset</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>color<span class='o'>=</span><span class='nv'>type</span>, group<span class='o'>=</span><span class='nv'>type</span>, x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>CumulativeIncidence</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Cumulative Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>labels<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span>,limits<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='kc'>NA</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>scale_color_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"darkgreen"</span>,<span class='s'>"red"</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-11-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggsave</span><span class='o'>(</span><span class='s'>"comparison.png"</span>,type<span class='o'>=</span><span class='s'>"cairo"</span>,width<span class='o'>=</span><span class='m'>7</span>,height<span class='o'>=</span><span class='m'>3</span><span class='o'>)</span>
</code></pre>

</div>

