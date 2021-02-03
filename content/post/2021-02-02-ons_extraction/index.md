---
title: "Democratising ONS infection survey data using SVG parsing"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-02-03T10:18:37Z
featured: false
draft: false
projects: []
mininote: true
output: 
  hugodown::md_document:
    fig_width: 6 
    fig_asp: 0.59
rmd_hash: 5b90def42dba3378

---

**Summary:** The ONS Coronavirus Infection Survey is an immensely valuable scientific project measuring prevalence of coronavirus in the community. However some useful data have only been released in graphical form which makes them hard to re-analyse. I extracted a line-list like dataset containing: swab date, overall Ct value, symptomatic status, and number of genes detected (1, 2 or 3) from a [preprint](https://www.medrxiv.org/content/10.1101/2020.10.25.20219048v1) describing testing work from 26 April--11 October 2020. Here I [make this data available](/ons_ct_values_and_symptoms.csv) for further analysis, and describe the extraction process.

<hr>

### Background

The ONS infection survey is an extremely valuable and well-conducted piece of work.

As I discussed in [a previous post](/post/2021-01-22-ons-data/) sometimes the raw data outputs can be subject to different interpretations.

![](ons_scatterplot.svg)

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>knitr</span><span class='nf'>::</span><span class='nv'><a href='https://rdrr.io/pkg/knitr/man/opts_chunk.html'>opts_chunk</a></span><span class='o'>$</span><span class='nf'>set</span><span class='o'>(</span>dev.args <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/list.html'>list</a></span><span class='o'>(</span>png <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/list.html'>list</a></span><span class='o'>(</span>type <span class='o'>=</span> <span class='s'>"cairo"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>require</a></span><span class='o'>(</span><span class='nv'><a href='https://xml2.r-lib.org/'>xml2</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://tidyverse.tidyverse.org'>tidyverse</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://lubridate.tidyverse.org'>lubridate</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://rvest.tidyverse.org/'>rvest</a></span><span class='o'>)</span>

</code></pre>

</div>

First we will read in the SVG file as XML and look at the straight lines - i.e. the axis plots and so on.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>theme_set</span><span class='o'>(</span><span class='nf'>theme_classic</span><span class='o'>(</span><span class='o'>)</span><span class='o'>)</span>

<span class='nv'>doc</span> <span class='o'>&lt;-</span> <span class='nf'><a href='http://xml2.r-lib.org/reference/read_xml.html'>read_xml</a></span><span class='o'>(</span><span class='s'>"ons_scatterplot.svg"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='http://xml2.r-lib.org/reference/xml_ns_strip.html'>xml_ns_strip</a></span><span class='o'>(</span><span class='o'>)</span>
<span class='nv'>lines</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rvest.tidyverse.org/reference/xml.html'>xml_nodes</a></span><span class='o'>(</span><span class='nv'>doc</span>, <span class='s'>"line"</span><span class='o'>)</span>

<span class='nv'>linesdf</span> <span class='o'>&lt;-</span>
  <span class='nf'>tibble</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/lapply.html'>lapply</a></span><span class='o'>(</span><span class='nf'><a href='http://xml2.r-lib.org/reference/xml_attr.html'>xml_attrs</a></span><span class='o'>(</span><span class='nv'>lines</span><span class='o'>)</span>, <span class='nv'>as.data.frame.list</span><span class='o'>)</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate_at</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"x1"</span>, <span class='s'>"x2"</span>, <span class='s'>"y1"</span>, <span class='s'>"y2"</span><span class='o'>)</span>, <span class='nv'>as.character</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate_at</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"x1"</span>, <span class='s'>"x2"</span>, <span class='s'>"y1"</span>, <span class='s'>"y2"</span><span class='o'>)</span>, <span class='nv'>as.numeric</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>y1 <span class='o'>=</span> <span class='o'>-</span><span class='nv'>y1</span>, y2 <span class='o'>=</span> <span class='o'>-</span><span class='nv'>y2</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>length <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/MathFun.html'>sqrt</a></span><span class='o'>(</span><span class='o'>(</span><span class='nv'>x2</span> <span class='o'>-</span> <span class='nv'>x1</span><span class='o'>)</span><span class='o'>^</span><span class='m'>2</span> <span class='o'>+</span> <span class='o'>(</span><span class='nv'>y2</span> <span class='o'>-</span> <span class='nv'>y1</span><span class='o'>)</span><span class='o'>^</span><span class='m'>2</span><span class='o'>)</span><span class='o'>)</span>
</code></pre>

</div>

Let's try drawing this set of lines.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>linesdf</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x1</span>, xend <span class='o'>=</span> <span class='nv'>x2</span>, y <span class='o'>=</span> <span class='nv'>y1</span>, yend <span class='o'>=</span> <span class='nv'>y2</span>, color <span class='o'>=</span> <span class='nv'>class</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_segment</span><span class='o'>(</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-3-1.png" width="700px" style="display: block; margin: auto;" />

</div>

OK, so `st14` represents the black lines of the axis - lets extract those some more and mark them as horizontal or vertical.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nv'>black_lines</span> <span class='o'>&lt;-</span> <span class='nv'>linesdf</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>class</span> <span class='o'>==</span> <span class='s'>"st14"</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>horizontal <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/MathFun.html'>abs</a></span><span class='o'>(</span><span class='nv'>y2</span> <span class='o'>-</span> <span class='nv'>y1</span><span class='o'>)</span> <span class='o'>&lt;</span> <span class='m'>10</span> <span class='o'>*</span> <span class='nf'><a href='https://rdrr.io/r/base/MathFun.html'>abs</a></span><span class='o'>(</span><span class='nv'>x2</span> <span class='o'>-</span> <span class='nv'>x1</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>vertical <span class='o'>=</span> <span class='o'>!</span><span class='nv'>horizontal</span><span class='o'>)</span>

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>black_lines</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x1</span>, xend <span class='o'>=</span> <span class='nv'>x2</span>, y <span class='o'>=</span> <span class='nv'>y1</span>, yend <span class='o'>=</span> <span class='nv'>y2</span>, color <span class='o'>=</span> <span class='nv'>horizontal</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_segment</span><span class='o'>(</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-4-1.png" width="700px" style="display: block; margin: auto;" />

</div>

We can also look at the distribution of line lengths to be able to separate out the short 'ticks'.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>black_lines</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>length</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_histogram</span><span class='o'>(</span><span class='o'>)</span>

<span class='c'>#&gt; `stat_bin()` using `bins = 30`. Pick better value with `binwidth`.</span>

</code></pre>
<img src="figs/unnamed-chunk-5-1.png" width="700px" style="display: block; margin: auto;" />

</div>

It looks like any line with length less than 30 will be a tick.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nv'>ticks</span> <span class='o'>&lt;-</span> <span class='nv'>black_lines</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>is_tick <span class='o'>=</span> <span class='nv'>length</span> <span class='o'>&lt;</span> <span class='m'>30</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>is_tick</span><span class='o'>)</span>
<span class='nv'>horiz_limits</span> <span class='o'>&lt;-</span> <span class='nv'>ticks</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>horizontal</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>y1</span> <span class='o'>==</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>max</a></span><span class='o'>(</span><span class='nv'>y1</span><span class='o'>)</span> <span class='o'>|</span> <span class='nv'>y1</span> <span class='o'>==</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>y1</span><span class='o'>)</span><span class='o'>)</span>
<span class='nv'>vert_limits</span> <span class='o'>&lt;-</span> <span class='nv'>ticks</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>vertical</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>x1</span> <span class='o'>==</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>max</a></span><span class='o'>(</span><span class='nv'>x1</span><span class='o'>)</span> <span class='o'>|</span> <span class='nv'>x1</span> <span class='o'>==</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>x1</span><span class='o'>)</span><span class='o'>)</span>


<span class='c'>#ggplot(bind_rows(horiz_limits, vert_limits), aes(x = x1, xend = x2, y = y1, yend = y2, color = horizontal)) +</span>
<span class='c'>#  geom_segment()</span>
</code></pre>

</div>

Now we can extract the positions of the maximum and minimum tick for each axis, and manually write in the real values those correspond to from the label:

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>x_min_svg</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>vert_limits</span><span class='o'>$</span><span class='nv'>x1</span><span class='o'>)</span>
<span class='nv'>x_max_svg</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>max</a></span><span class='o'>(</span><span class='nv'>vert_limits</span><span class='o'>$</span><span class='nv'>x1</span><span class='o'>)</span>
<span class='nv'>y_min_svg</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>min</a></span><span class='o'>(</span><span class='nv'>horiz_limits</span><span class='o'>$</span><span class='nv'>y1</span><span class='o'>)</span>
<span class='nv'>y_max_svg</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/Extremes.html'>max</a></span><span class='o'>(</span><span class='nv'>horiz_limits</span><span class='o'>$</span><span class='nv'>y1</span><span class='o'>)</span>

<span class='nv'>x_min_real</span> <span class='o'>&lt;-</span> <span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-04-26"</span><span class='o'>)</span>
<span class='nv'>x_max_real</span> <span class='o'>&lt;-</span> <span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-10-11"</span><span class='o'>)</span>
<span class='nv'>y_min_real</span> <span class='o'>&lt;-</span> <span class='m'>10</span>
<span class='nv'>y_max_real</span> <span class='o'>&lt;-</span> <span class='m'>40</span>
</code></pre>

</div>

We've dealt with the axes. Now time to move onto the points. Unfortunately they are not points, they are paths (bezier-curves), drawing circles to represent points!

Here is a [useful tool for interpreting SVG commands](https://svg-path-visualizer.netlify.app/)

We'll extract all the paths  

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>paths</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rvest.tidyverse.org/reference/xml.html'>xml_nodes</a></span><span class='o'>(</span><span class='nv'>doc</span>, <span class='s'>"path"</span><span class='o'>)</span>
</code></pre>

</div>

And then try to split up the bezier curves into sub commands, like `M` (move to a point), `C` draw a curve in absolute coordinates, `c` draw a curve in relative coordinates, and so on.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>pathsdf</span> <span class='o'>&lt;-</span>
  <span class='nf'>tibble</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/lapply.html'>lapply</a></span><span class='o'>(</span><span class='nf'><a href='http://xml2.r-lib.org/reference/xml_attr.html'>xml_attrs</a></span><span class='o'>(</span><span class='nv'>paths</span><span class='o'>)</span>, <span class='nv'>as.data.frame.list</span><span class='o'>)</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>id <span class='o'>=</span> <span class='m'>1</span><span class='o'>:</span><span class='nf'>n</span><span class='o'>(</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"S"</span>, <span class='s'>"|S"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"s"</span>, <span class='s'>"|s"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"z"</span>, <span class='s'>"|z"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"c"</span>, <span class='s'>"|c:"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"C"</span>, <span class='s'>"|C:"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"M"</span>, <span class='s'>"M:"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"\n"</span>, <span class='s'>""</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>" "</span>, <span class='s'>""</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>d <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/grep.html'>gsub</a></span><span class='o'>(</span><span class='s'>"([0-9])-"</span>, <span class='s'>"\\1,-"</span>, <span class='nv'>d</span><span class='o'>)</span><span class='o'>)</span>



<span class='nv'>commandsdf</span> <span class='o'>&lt;-</span> <span class='nv'>pathsdf</span> <span class='o'>%&gt;%</span>
  <span class='nf'>separate_rows</span><span class='o'>(</span><span class='nv'>d</span>, sep <span class='o'>=</span> <span class='s'>"\\|"</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>d</span> <span class='o'>!=</span> <span class='s'>"z"</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>separate</span><span class='o'>(</span><span class='nv'>d</span>, into <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"command"</span>, <span class='s'>"parameters"</span><span class='o'>)</span>, <span class='s'>":"</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Expected 2 pieces. Missing pieces filled with `NA` in 24 rows [23, 25, 28, 30, 7933, 7935, 7938, 7940, 8673, 8675, 8678, 8680, 16233, 16235, 16238, 16240, 16243, 16245, 16248, 16250, ...].</span>




<span class='nv'>commandsdf</span> <span class='o'>%&gt;%</span>
  <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>id</span>, <span class='nv'>command</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>summarise</span><span class='o'>(</span>n <span class='o'>=</span> <span class='nf'>n</span><span class='o'>(</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>n</span>, <span class='nv'>command</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>summarise</span><span class='o'>(</span>count <span class='o'>=</span> <span class='nf'>n</span><span class='o'>(</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; `summarise()` regrouping output by 'id' (override with `.groups` argument)</span>

<span class='c'>#&gt; `summarise()` regrouping output by 'n' (override with `.groups` argument)</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 15 x 3</span></span>
<span class='c'>#&gt; <span style='color: #555555;'># Groups:   n [3]</span></span>
<span class='c'>#&gt;        n command                  count</span>
<span class='c'>#&gt;    <span style='color: #555555;font-style: italic;'>&lt;int&gt;</span><span> </span><span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>                    </span><span style='color: #555555;font-style: italic;'>&lt;int&gt;</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 1</span><span>     1 C                         </span><span style='text-decoration: underline;'>3</span><span>784</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 2</span><span>     1 M                         </span><span style='text-decoration: underline;'>3</span><span>796</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 3</span><span>     1 s-0.72,0.36,-0.72,0.72       2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 4</span><span>     1 s-0.72,0.36,-0.72,0.9        4</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 5</span><span>     1 S43,38.37,43,38.91           2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 6</span><span>     1 S43,54.21,43,54.75           2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 7</span><span>     1 S43,75.45,43,75.99           2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 8</span><span>     1 S44.44,39.27,44.44,38.91     2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 9</span><span>     1 S44.44,55.29,44.44,54.75     2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>10</span><span>     1 S44.44,76.53,44.44,75.99     2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>11</span><span>     1 S55.78,40.89,55.78,40.53     2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>12</span><span>     1 S55.78,44.49,55.78,44.13     2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>13</span><span>     1 S55.78,62.49,55.78,62.13     2</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>14</span><span>     2 c                           12</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>15</span><span>     3 c                         </span><span style='text-decoration: underline;'>3</span><span>784</span></span>



<span class='nv'>commands_processed</span> <span class='o'>&lt;-</span> <span class='nv'>commandsdf</span> <span class='o'>%&gt;%</span>
  <span class='nf'>separate</span><span class='o'>(</span><span class='nv'>parameters</span>, into <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"p1"</span>, <span class='s'>"p2"</span>, <span class='s'>"p3"</span>, <span class='s'>"p4"</span>, <span class='s'>"p5"</span>, <span class='s'>"p6"</span><span class='o'>)</span>, sep <span class='o'>=</span> <span class='s'>","</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>abs_x <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"M"</span> <span class='o'>~</span> <span class='nv'>p1</span>, <span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"C"</span> <span class='o'>~</span> <span class='nv'>p5</span><span class='o'>)</span>, abs_y <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"M"</span> <span class='o'>~</span> <span class='nv'>p2</span>, <span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"C"</span> <span class='o'>~</span> <span class='nv'>p6</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>rel_x <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"c"</span> <span class='o'>~</span> <span class='nv'>p5</span><span class='o'>)</span>, rel_y <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"c"</span> <span class='o'>~</span> <span class='nv'>p6</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Expected 6 pieces. Missing pieces filled with `NA` in 3796 rows [1, 6, 11, 16, 21, 26, 31, 36, 41, 46, 51, 56, 61, 66, 71, 76, 81, 86, 91, 96, ...].</span>
</code></pre>

</div>

These circles are represented as 4 curves, with 4 control points. If we take the average of these we can find the position of the point. One of the points in just the `M` coordinates, but 3 are from `c` commands, and they only have relative coordinates, so we need to calculate the cumulative sums of these, and then add them to the `M` coordinates. Then we average out, and add back the class metadata.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>lowercase_cs</span> <span class='o'>&lt;-</span> <span class='nv'>commands_processed</span> <span class='o'>%&gt;%</span>
  <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>id</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"c"</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>rel_x_sum <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/cumsum.html'>cumsum</a></span><span class='o'>(</span><span class='nv'>rel_x</span><span class='o'>)</span>, rel_y_sum <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/cumsum.html'>cumsum</a></span><span class='o'>(</span><span class='nv'>rel_y</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>select</span><span class='o'>(</span><span class='nv'>id</span>, <span class='nv'>rel_x_sum</span>, <span class='nv'>rel_y_sum</span><span class='o'>)</span>

<span class='nv'>zeroes</span> <span class='o'>&lt;-</span> <span class='nv'>lowercase_cs</span> <span class='o'>%&gt;%</span>
  <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>id</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>summarise</span><span class='o'>(</span>rel_x_sum <span class='o'>=</span> <span class='m'>0</span>, rel_y_sum <span class='o'>=</span> <span class='m'>0</span><span class='o'>)</span>

<span class='c'>#&gt; `summarise()` ungrouping output (override with `.groups` argument)</span>


<span class='nv'>full_relative_set</span> <span class='o'>&lt;-</span> <span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>lowercase_cs</span>, <span class='nv'>zeroes</span><span class='o'>)</span>

<span class='nv'>uppercase_cs</span> <span class='o'>&lt;-</span> <span class='nv'>commands_processed</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"C"</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>select</span><span class='o'>(</span><span class='nv'>id</span>, <span class='nv'>command</span>, <span class='nv'>abs_x</span>, <span class='nv'>abs_y</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>abs_x <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/numeric.html'>as.numeric</a></span><span class='o'>(</span><span class='nv'>abs_x</span><span class='o'>)</span>, abs_y <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/numeric.html'>as.numeric</a></span><span class='o'>(</span><span class='nv'>abs_y</span><span class='o'>)</span><span class='o'>)</span>

<span class='nv'>both</span> <span class='o'>&lt;-</span> <span class='nf'>inner_join</span><span class='o'>(</span><span class='nv'>uppercase_cs</span>, <span class='nv'>full_relative_set</span>, by <span class='o'>=</span> <span class='s'>"id"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>abs_x</span> <span class='o'>+</span> <span class='nv'>rel_x_sum</span>, y <span class='o'>=</span> <span class='nv'>abs_y</span> <span class='o'>+</span> <span class='nv'>rel_y_sum</span><span class='o'>)</span>

<span class='nv'>point_types</span> <span class='o'>&lt;-</span> <span class='nv'>pathsdf</span> <span class='o'>%&gt;%</span> <span class='nf'>select</span><span class='o'>(</span><span class='nv'>id</span>, <span class='nv'>class</span><span class='o'>)</span>

<span class='nv'>points</span> <span class='o'>&lt;-</span> <span class='nv'>both</span> <span class='o'>%&gt;%</span>
  <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>id</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>summarise</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/mean.html'>mean</a></span><span class='o'>(</span><span class='nv'>x</span><span class='o'>)</span>, y <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/mean.html'>mean</a></span><span class='o'>(</span><span class='nv'>y</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>inner_join</span><span class='o'>(</span><span class='nv'>point_types</span>, by <span class='o'>=</span> <span class='s'>"id"</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>y <span class='o'>=</span> <span class='o'>-</span><span class='nv'>y</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>class</span> <span class='o'>%in%</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"st10"</span>, <span class='s'>"st7"</span>, <span class='s'>"st9"</span>, <span class='s'>"st12"</span>, <span class='s'>"st11"</span>, <span class='s'>"st5"</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; `summarise()` ungrouping output (override with `.groups` argument)</span>
</code></pre>

</div>

Above you can see I have filtered to only some of the classes. This is because some points were represented by two curves, one a stroke and one a fill. Now let's plot the points.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>black_lines</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x1</span>, xend <span class='o'>=</span> <span class='nv'>x2</span>, y <span class='o'>=</span> <span class='nv'>y1</span>, yend <span class='o'>=</span> <span class='nv'>y2</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_segment</span><span class='o'>(</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_point</span><span class='o'>(</span>data <span class='o'>=</span> <span class='nv'>points</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>x2 <span class='o'>=</span> <span class='m'>0</span>, y2 <span class='o'>=</span> <span class='m'>0</span><span class='o'>)</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x</span>, y <span class='o'>=</span> <span class='nv'>y</span>, color <span class='o'>=</span> <span class='nv'>class</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_color_brewer</span><span class='o'>(</span>type <span class='o'>=</span> <span class='s'>"qual"</span>, palette <span class='o'>=</span> <span class='s'>"Paired"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-11-1.png" width="700px" style="display: block; margin: auto;" />

</div>

Conveniently, the points in the key are still there, so we can easily label these classes with their correct metadata

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>genes_detected</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>1</span>, <span class='m'>1</span>, <span class='m'>2</span>, <span class='m'>2</span>, <span class='m'>3</span>, <span class='m'>3</span><span class='o'>)</span>
<span class='nv'>symptoms</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"yes"</span>, <span class='s'>"no"</span>, <span class='s'>"yes"</span>, <span class='s'>"no"</span>, <span class='s'>"yes"</span>, <span class='s'>"no"</span><span class='o'>)</span>
<span class='nv'>classes</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"st9"</span>, <span class='s'>"st12"</span>, <span class='s'>"st7"</span>, <span class='s'>"st11"</span>, <span class='s'>"st5"</span>, <span class='s'>"st10"</span><span class='o'>)</span>

<span class='nv'>class_info</span> <span class='o'>&lt;-</span> <span class='nf'>tibble</span><span class='o'>(</span>class <span class='o'>=</span> <span class='nv'>classes</span>, symptoms <span class='o'>=</span> <span class='nv'>symptoms</span>, genes_detected <span class='o'>=</span> <span class='nv'>genes_detected</span><span class='o'>)</span>


<span class='nv'>points_detail</span> <span class='o'>&lt;-</span> <span class='nv'>points</span> <span class='o'>%&gt;%</span>
  <span class='nf'>inner_join</span><span class='o'>(</span><span class='nv'>class_info</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>genes_detected <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/factor.html'>as.factor</a></span><span class='o'>(</span><span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; Joining, by = "class"</span>


<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>black_lines</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x1</span>, xend <span class='o'>=</span> <span class='nv'>x2</span>, y <span class='o'>=</span> <span class='nv'>y1</span>, yend <span class='o'>=</span> <span class='nv'>y2</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_segment</span><span class='o'>(</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_point</span><span class='o'>(</span>data <span class='o'>=</span> <span class='nv'>points_detail</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>x2 <span class='o'>=</span> <span class='m'>0</span>, y2 <span class='o'>=</span> <span class='m'>0</span><span class='o'>)</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x</span>, y <span class='o'>=</span> <span class='nv'>y</span>, color <span class='o'>=</span> <span class='nv'>genes_detected</span>, shape <span class='o'>=</span> <span class='nv'>symptoms</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_color_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_shape_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>1</span>,<span class='m'>16</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-12-1.png" width="700px" style="display: block; margin: auto;" />

</div>

Getting there!

Now we just need to transform ourselves into the right axes:

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nv'>transform</span> <span class='o'>&lt;-</span> <span class='kr'>function</span><span class='o'>(</span><span class='nv'>vector</span>, <span class='nv'>a_in</span>, <span class='nv'>a_out</span>, <span class='nv'>b_in</span>, <span class='nv'>b_out</span><span class='o'>)</span> <span class='o'>&#123;</span>
  <span class='nv'>scaling</span> <span class='o'>&lt;-</span> <span class='o'>(</span><span class='nv'>b_out</span> <span class='o'>-</span> <span class='nv'>a_out</span><span class='o'>)</span> <span class='o'>/</span> <span class='o'>(</span><span class='nv'>b_in</span> <span class='o'>-</span> <span class='nv'>a_in</span><span class='o'>)</span>
  <span class='nv'>vector</span> <span class='o'>&lt;-</span> <span class='o'>(</span><span class='nv'>vector</span> <span class='o'>-</span> <span class='nv'>a_in</span><span class='o'>)</span> <span class='o'>*</span> <span class='nv'>scaling</span> <span class='o'>+</span> <span class='nv'>a_out</span>
  <span class='kr'><a href='https://rdrr.io/r/base/function.html'>return</a></span><span class='o'>(</span><span class='nv'>vector</span><span class='o'>)</span>
<span class='o'>&#125;</span>


<span class='nv'>ytransform</span> <span class='o'>&lt;-</span> <span class='nf'>partial</span><span class='o'>(</span><span class='nv'>transform</span>, a_in <span class='o'>=</span> <span class='nv'>y_min_svg</span>, a_out <span class='o'>=</span> <span class='nv'>y_min_real</span>, b_in <span class='o'>=</span> <span class='nv'>y_max_svg</span>, b_out <span class='o'>=</span> <span class='nv'>y_max_real</span><span class='o'>)</span>
<span class='nv'>xtransform</span> <span class='o'>&lt;-</span> <span class='nf'>partial</span><span class='o'>(</span><span class='nv'>transform</span>, a_in <span class='o'>=</span> <span class='nv'>x_min_svg</span>, a_out <span class='o'>=</span> <span class='nv'>x_min_real</span>, b_in <span class='o'>=</span> <span class='nv'>x_max_svg</span>, b_out <span class='o'>=</span> <span class='nv'>x_max_real</span><span class='o'>)</span>


<span class='nv'>new_axes</span> <span class='o'>&lt;-</span> <span class='nv'>black_lines</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>y1 <span class='o'>=</span> <span class='nf'>ytransform</span><span class='o'>(</span><span class='nv'>y1</span><span class='o'>)</span>, y2 <span class='o'>=</span> <span class='nf'>ytransform</span><span class='o'>(</span><span class='nv'>y2</span><span class='o'>)</span>, x1 <span class='o'>=</span> <span class='nf'>xtransform</span><span class='o'>(</span><span class='nv'>x1</span><span class='o'>)</span>, x2 <span class='o'>=</span> <span class='nf'>xtransform</span><span class='o'>(</span><span class='nv'>x2</span><span class='o'>)</span><span class='o'>)</span>

<span class='nv'>points_transformed</span> <span class='o'>&lt;-</span> <span class='nv'>points_detail</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>y <span class='o'>=</span> <span class='nf'>ytransform</span><span class='o'>(</span><span class='nv'>y</span><span class='o'>)</span>, x <span class='o'>=</span> <span class='nf'>xtransform</span><span class='o'>(</span><span class='nv'>x</span><span class='o'>)</span><span class='o'>)</span>


<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>new_axes</span>, <span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_segment</span><span class='o'>(</span><span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x1</span>, xend <span class='o'>=</span> <span class='nv'>x2</span>, y <span class='o'>=</span> <span class='nv'>y1</span>, yend <span class='o'>=</span> <span class='nv'>y2</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_point</span><span class='o'>(</span>data <span class='o'>=</span> <span class='nv'>points_transformed</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>x2 <span class='o'>=</span> <span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-01-01"</span><span class='o'>)</span>, y2 <span class='o'>=</span> <span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-01-01"</span><span class='o'>)</span><span class='o'>)</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x</span>, y <span class='o'>=</span> <span class='nv'>y</span>, color <span class='o'>=</span> <span class='nv'>genes_detected</span>, shape <span class='o'>=</span> <span class='nv'>symptoms</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_color_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_shape_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>1</span>,<span class='m'>16</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-13-1.png" width="700px" style="display: block; margin: auto;" />

</div>

OK, at this point we can throw away the axes and just focus on the points.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>values</span> <span class='o'>&lt;-</span> <span class='nv'>points_transformed</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>y</span> <span class='o'>&gt;</span> <span class='m'>0</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>Date <span class='o'>=</span> <span class='nv'>x</span>, Ct <span class='o'>=</span> <span class='nv'>y</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>select</span><span class='o'>(</span><span class='o'>-</span><span class='nv'>x</span>, <span class='o'>-</span><span class='nv'>y</span>, <span class='o'>-</span><span class='nv'>class</span>,<span class='o'>-</span><span class='nv'>id</span><span class='o'>)</span>

<span class='nf'>write_csv</span><span class='o'>(</span><span class='nv'>values</span>, <span class='s'>"extracted_ct_value_genes_detected_and_symptoms.csv"</span><span class='o'>)</span>
<span class='nv'>values</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 1,886 x 4</span></span>
<span class='c'>#&gt;    symptoms genes_detected Date          Ct</span>
<span class='c'>#&gt;    <span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>    </span><span style='color: #555555;font-style: italic;'>&lt;fct&gt;</span><span>          </span><span style='color: #555555;font-style: italic;'>&lt;date&gt;</span><span>     </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 1</span><span> yes      3              2020-04-28  26.8</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 2</span><span> yes      3              2020-04-28  21.7</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 3</span><span> yes      3              2020-05-07  22.9</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 4</span><span> yes      3              2020-05-09  29.4</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 5</span><span> yes      3              2020-05-10  21.8</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 6</span><span> yes      3              2020-05-10  27.3</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 7</span><span> yes      3              2020-05-11  18.1</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 8</span><span> yes      3              2020-05-11  16.8</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 9</span><span> yes      3              2020-05-11  27.3</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>10</span><span> yes      3              2020-05-13  19.3</span></span>
<span class='c'>#&gt; <span style='color: #555555;'># … with 1,876 more rows</span></span>m
</code></pre>

</div>

And there's our dataset.

And here's our graph

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Date</span>,y<span class='o'>=</span><span class='nv'>Ct</span>,color<span class='o'>=</span><span class='nv'>genes_detected</span>,shape<span class='o'>=</span><span class='nv'>symptoms</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_color_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_shape_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>1</span>,<span class='m'>16</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_point</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span> <span class='nf'>theme</span><span class='o'>(</span>legend.position<span class='o'>=</span><span class='s'>"bottom"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-15-1.png" width="700px" style="display: block; margin: auto;" />

</div>

as compared to ![](ons_scatterplot.svg)

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_density</span><span class='o'>(</span>alpha<span class='o'>=</span><span class='m'>0.3</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_grid</span><span class='o'>(</span><span class='nv'>symptoms</span><span class='o'>~</span><span class='nv'>.</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-16-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_density</span><span class='o'>(</span>position<span class='o'>=</span><span class='s'>"stack"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>facet_grid</span><span class='o'>(</span><span class='nv'>symptoms</span><span class='o'>~</span><span class='nv'>.</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-16-2.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>symptoms</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>geom_density</span><span class='o'>(</span>alpha<span class='o'>=</span><span class='m'>0.5</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-16-3.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_histogram</span><span class='o'>(</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nf'><a href='http://lubridate.tidyverse.org/reference/month.html'>month</a></span><span class='o'>(</span><span class='nv'>Date</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; `stat_bin()` using `bins = 30`. Pick better value with `binwidth`.</span>

</code></pre>
<img src="figs/unnamed-chunk-16-4.png" width="700px" style="display: block; margin: auto;" />

</div>

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>simulated_b117</span> <span class='o'>=</span> <span class='nv'>values</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>genes_detected<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/factor.html'>as.factor</a></span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/numeric.html'>as.numeric</a></span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/character.html'>as.character</a></span><span class='o'>(</span><span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span><span class='o'>-</span><span class='m'>1</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>genes_detected</span><span class='o'>!=</span> <span class='m'>0</span><span class='o'>)</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>simulated_b117</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_histogram</span><span class='o'>(</span><span class='o'>)</span> 

<span class='c'>#&gt; `stat_bin()` using `bins = 30`. Pick better value with `binwidth`.</span>

</code></pre>
<img src="figs/unnamed-chunk-17-1.png" width="700px" style="display: block; margin: auto;" />

</div>

