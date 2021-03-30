---
title: "Five lines of R"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-03-30T10:18:37Z
featured: false
draft: false
projects: []
mininote: true
output: 
  hugodown::md_document:
    fig_width: 6 
    fig_asp: 0.59
rmd_hash: b479eebbab42f045

---

Somebody on Twitter asked me whether B.1.1.7 data from Florida was still compatible with a logistic growth curve.

It's amazing how simple this sort of thing is to look at with the Tidyverse and nicely formatted data SGTF data from [Helix](https://github.com/myhelix/helix-covid19db).

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://tidyverse.tidyverse.org'>tidyverse</a></span><span class='o'>)</span>
<span class='nv'>data</span> <span class='o'>=</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/connections.html'>url</a></span><span class='o'>(</span><span class='s'>"https://raw.githubusercontent.com/myhelix/helix-covid19db/master/counts_by_state.csv"</span><span class='o'>)</span><span class='o'>)</span>
<span class='nv'>state_selection</span> <span class='o'>=</span> <span class='o'>(</span><span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>state</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>summarise</span><span class='o'>(</span>total<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/sum.html'>sum</a></span><span class='o'>(</span><span class='nv'>positive</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>total</span><span class='o'>&gt;</span><span class='m'>5000</span><span class='o'>)</span><span class='o'>)</span><span class='o'>$</span><span class='nv'>state</span>
<span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span>  <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>percent_sgtf<span class='o'>=</span><span class='nv'>all_SGTF</span><span class='o'>/</span><span class='nv'>positive</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>state</span> <span class='o'>%in%</span> <span class='nv'>state_selection</span><span class='o'>)</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>data</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>collection_date</span>, y<span class='o'>=</span><span class='nv'>percent_sgtf</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_point</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span> <span class='nf'>stat_smooth</span><span class='o'>(</span>method <span class='o'>=</span> <span class='s'>"glm"</span>, method.args <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/list.html'>list</a></span><span class='o'>(</span>family <span class='o'>=</span> <span class='s'>"binomial"</span><span class='o'>)</span>, se <span class='o'>=</span> <span class='kc'>FALSE</span>,  fullrange<span class='o'>=</span><span class='kc'>TRUE</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'><a href='https://rdrr.io/r/graphics/plot.window.html'>xlim</a></span><span class='o'>(</span><span class='nf'>lubridate</span><span class='nf'>::</span><span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-12-01"</span><span class='o'>)</span>,<span class='nf'>lubridate</span><span class='nf'>::</span><span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2021-04-30"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>title<span class='o'>=</span><span class='s'>"US SGTF"</span>,x<span class='o'>=</span><span class='s'>"Date"</span>,y<span class='o'>=</span><span class='s'>"Percent SGTF"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nv'>state</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>label<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org/reference/label_percent.html'>percent</a></span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-1-1.png" width="700px" style="display: block; margin: auto;" />

</div>

There are lots of ways one could improve this, bringing in genome data and modelling uncertainty, but it provides a quick look at what's happening.

