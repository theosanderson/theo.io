---
title: "Turning a graph back into data"
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
rmd_hash: 229cd0ad5c48c5db

---

**Summary:** The ONS Coronavirus Infection Survey is an immensely valuable scientific project measuring prevalence of coronavirus in the community. However some useful data have been released only in graphical form which makes them hard to re-analyse. Here I describe how I went about turning one of these graphs back into data in R and briefly explore what it can tell us about SGTF in samples today.

<hr>

### Background

The ONS infection survey is an extremely valuable and well-conducted piece of work. As I discussed in [a previous post](/post/2021-01-22-ons-data/) sometimes the raw data outputs can be subject to different interpretations.

One challenge in interpreting this data in that post was that information on the relationship between the number of genes detected and the distribution of Ct values was not available. I resorted to examining this by calculating the Ct value in different regions, and relating this to the area's mean Ct value. But what I really wanted was a line list containing Ct value data for each test, along with how many genes were detected (and ideally further information).

I have since discovered that some of this data is available in [a preprint](https://www.medrxiv.org/content/10.1101/2020.10.25.20219048v1) published by the survey team in October 2020. Specifically, for me - the most valuable data is the graph below, which depicts individual tests with their Ct values, symptomatic status, and number of genes detected.

![](ons_scatterplot.svg)

Unfortunately this data is only presented graphically. But since the graphic is present in a vector format we may be able to get the data back out again without locating every point by hand. Here is how I went about this.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>knitr</span><span class='nf'>::</span><span class='nv'><a href='https://rdrr.io/pkg/knitr/man/opts_chunk.html'>opts_chunk</a></span><span class='o'>$</span><span class='nf'>set</span><span class='o'>(</span>dev.args <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/list.html'>list</a></span><span class='o'>(</span>png <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/list.html'>list</a></span><span class='o'>(</span>type <span class='o'>=</span> <span class='s'>"cairo"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>require</a></span><span class='o'>(</span><span class='nv'><a href='https://xml2.r-lib.org/'>xml2</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://tidyverse.tidyverse.org'>tidyverse</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://lubridate.tidyverse.org'>lubridate</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://rvest.tidyverse.org/'>rvest</a></span><span class='o'>)</span>

</code></pre>

</div>

First we will read in the SVG file as XML and look at the straight lines - i.e. the axes, things like the axes and tick-lines.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>theme_set</span><span class='o'>(</span><span class='nf'>theme_classic</span><span class='o'>(</span><span class='o'>)</span><span class='o'>)</span>

<span class='nv'>doc</span> <span class='o'>&lt;-</span> <span class='nf'><a href='http://xml2.r-lib.org/reference/read_xml.html'>read_xml</a></span><span class='o'>(</span><span class='s'>"ons_scatterplot.svg"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='http://xml2.r-lib.org/reference/xml_ns_strip.html'>xml_ns_strip</a></span><span class='o'>(</span><span class='o'>)</span>
<span class='nv'>lines</span> <span class='o'>&lt;-</span> <span class='nf'><a href='https://rvest.tidyverse.org/reference/xml.html'>xml_nodes</a></span><span class='o'>(</span><span class='nv'>doc</span>, <span class='s'>"line"</span><span class='o'>)</span>

<span class='nv'>linesdf</span> <span class='o'>&lt;-</span>
  <span class='nf'>tibble</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/lapply.html'>lapply</a></span><span class='o'>(</span><span class='nf'><a href='http://xml2.r-lib.org/reference/xml_attr.html'>xml_attrs</a></span><span class='o'>(</span><span class='nv'>lines</span><span class='o'>)</span>, <span class='nv'>as.data.frame.list</span><span class='o'>)</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate_at</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"x1"</span>, <span class='s'>"x2"</span>, <span class='s'>"y1"</span>, <span class='s'>"y2"</span><span class='o'>)</span>, <span class='nv'>as.character</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate_at</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"x1"</span>, <span class='s'>"x2"</span>, <span class='s'>"y1"</span>, <span class='s'>"y2"</span><span class='o'>)</span>, <span class='nv'>as.numeric</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>y1 <span class='o'>=</span> <span class='o'>-</span><span class='nv'>y1</span>, y2 <span class='o'>=</span> <span class='o'>-</span><span class='nv'>y2</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='c'># y-axis is reversed in SVG - screen format is from top left</span>
  <span class='nf'>mutate</span><span class='o'>(</span>length <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/MathFun.html'>sqrt</a></span><span class='o'>(</span><span class='o'>(</span><span class='nv'>x2</span> <span class='o'>-</span> <span class='nv'>x1</span><span class='o'>)</span><span class='o'>^</span><span class='m'>2</span> <span class='o'>+</span> <span class='o'>(</span><span class='nv'>y2</span> <span class='o'>-</span> <span class='nv'>y1</span><span class='o'>)</span><span class='o'>^</span><span class='m'>2</span><span class='o'>)</span><span class='o'>)</span>
</code></pre>

</div>

Let's try drawing this set of lines in ggplot. We'll colour them in by their SVG class to see what class corresponds to which items of the plot.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>linesdf</span>, <span class='nf'>aes</span><span class='o'>(</span>x <span class='o'>=</span> <span class='nv'>x1</span>, xend <span class='o'>=</span> <span class='nv'>x2</span>, y <span class='o'>=</span> <span class='nv'>y1</span>, yend <span class='o'>=</span> <span class='nv'>y2</span>, color <span class='o'>=</span> <span class='nv'>class</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>geom_segment</span><span class='o'>(</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-3-1.png" width="700px" style="display: block; margin: auto;" />

</div>

OK, so `st14` represents the black lines of the axis - lets extract those and mark them each as horizontal or vertical.

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
</code></pre>

</div>

We'll also manually enter the corresponding real values from the tick labels

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>x_min_real</span> <span class='o'>&lt;-</span> <span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-04-26"</span><span class='o'>)</span>
<span class='nv'>x_max_real</span> <span class='o'>&lt;-</span> <span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='s'>"2020-10-11"</span><span class='o'>)</span>
<span class='nv'>y_min_real</span> <span class='o'>&lt;-</span> <span class='m'>10</span>
<span class='nv'>y_max_real</span> <span class='o'>&lt;-</span> <span class='m'>40</span>
</code></pre>

</div>

We've dealt with the axes. Now time to move onto the points. Unfortunately they are not points, they are paths (bezier-curves), drawing circles to represent points!

Here is a [tool](https://svg-path-visualizer.netlify.app/) I found useful for interpreting SVG commands.

We'll extract all the paths.

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


<span class='nv'>commands_processed</span> <span class='o'>&lt;-</span> <span class='nv'>commandsdf</span> <span class='o'>%&gt;%</span>
  <span class='nf'>separate</span><span class='o'>(</span><span class='nv'>parameters</span>, into <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"p1"</span>, <span class='s'>"p2"</span>, <span class='s'>"p3"</span>, <span class='s'>"p4"</span>, <span class='s'>"p5"</span>, <span class='s'>"p6"</span><span class='o'>)</span>, sep <span class='o'>=</span> <span class='s'>","</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>abs_x <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"M"</span> <span class='o'>~</span> <span class='nv'>p1</span>, <span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"C"</span> <span class='o'>~</span> <span class='nv'>p5</span><span class='o'>)</span>, abs_y <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"M"</span> <span class='o'>~</span> <span class='nv'>p2</span>, <span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"C"</span> <span class='o'>~</span> <span class='nv'>p6</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>rel_x <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"c"</span> <span class='o'>~</span> <span class='nv'>p5</span><span class='o'>)</span>, rel_y <span class='o'>=</span> <span class='nf'>case_when</span><span class='o'>(</span><span class='nv'>command</span> <span class='o'>==</span> <span class='s'>"c"</span> <span class='o'>~</span> <span class='nv'>p6</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Expected 6 pieces. Missing pieces filled with `NA` in 3796 rows [1, 6, 11, 16, 21, 26, 31, 36, 41, 46, 51, 56, 61, 66, 71, 76, 81, 86, 91, 96, ...].</span>
</code></pre>

</div>

These circles in SVG form curves, with 4 control points. If we take the average of these we can find the position of the point. One of the points is absolute, from the `M` coordinates, but 3 are from `c` commands, and they only have relative coordinates, so we need to calculate the cumulative sums of these, and then add them to the `M` coordinates. Then we'll average out, and add back the class metadata.

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
<img src="figs/unnamed-chunk-12-1.png" width="700px" style="display: block; margin: auto;" />

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
<img src="figs/unnamed-chunk-13-1.png" width="700px" style="display: block; margin: auto;" />

</div>

We're getting there! We have recreated the chart within our ggplot.

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
<img src="figs/unnamed-chunk-14-1.png" width="700px" style="display: block; margin: auto;" />

</div>

OK, the coordinates look right. At this point we can throw away the old axes and just focus on the points.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>values</span> <span class='o'>&lt;-</span> <span class='nv'>points_transformed</span> <span class='o'>%&gt;%</span>
  <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>y</span> <span class='o'>&gt;</span> <span class='m'>0</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>mutate</span><span class='o'>(</span>Date <span class='o'>=</span> <span class='nv'>x</span>, Ct <span class='o'>=</span> <span class='nv'>y</span><span class='o'>)</span> <span class='o'>%&gt;%</span>
  <span class='nf'>select</span><span class='o'>(</span><span class='o'>-</span><span class='nv'>x</span>, <span class='o'>-</span><span class='nv'>y</span>, <span class='o'>-</span><span class='nv'>class</span>,<span class='o'>-</span><span class='nv'>id</span><span class='o'>)</span>

<span class='nf'>write_csv</span><span class='o'>(</span><span class='nv'>values</span>, <span class='s'>"ons_ct_value_genes_detected_and_symptoms.csv"</span><span class='o'>)</span>
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

There's our [dataset](ons_ct_value_genes_detected_and_symptoms.csv).

And here's our graph.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Date</span>,y<span class='o'>=</span><span class='nv'>Ct</span>,color<span class='o'>=</span><span class='nv'>genes_detected</span>,shape<span class='o'>=</span><span class='nv'>symptoms</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_color_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_shape_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>1</span>,<span class='m'>16</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>geom_point</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span> <span class='nf'>theme</span><span class='o'>(</span>legend.position<span class='o'>=</span><span class='s'>"bottom"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-16-1.png" width="700px" style="display: block; margin: auto;" />

</div>

as compared to: ![](ons_scatterplot.svg)

Now we can see what this data can tell us about Ct value distributions:

What is the distribution of 1, 2, and 3 gene positives at different Ct values?

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span>,y<span class='o'>=</span><span class='nv'>..count..</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>geom_density</span><span class='o'>(</span>alpha<span class='o'>=</span><span class='m'>0.3</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-17-1.png" width="700px" style="display: block; margin: auto;" />

</div>

What is the distribution of Ct values with and without symptoms?

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>symptoms</span>,y<span class='o'>=</span><span class='nv'>..count..</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>geom_density</span><span class='o'>(</span>alpha<span class='o'>=</span><span class='m'>0.3</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-18-1.png" width="700px" style="display: block; margin: auto;" />

</div>

Does symptomatic Ct distribution vary over time?

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Date</span>,y<span class='o'>=</span><span class='nv'>Ct</span>,color<span class='o'>=</span><span class='nv'>symptoms</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>geom_smooth</span><span class='o'>(</span><span class='o'>)</span>

<span class='c'>#&gt; `geom_smooth()` using method = 'gam' and formula 'y ~ s(x, bs = "cs")'</span>

</code></pre>
<img src="figs/unnamed-chunk-19-1.png" width="700px" style="display: block; margin: auto;" />

</div>

(yes, it does -- which I hadn't especially expected -- this may be because it includes symptoms either side of the test at any length of time, so at times of high Ct perhaps people aren't mostly symptomatic at the time of testing)

Crucially from the point of view of assessing B.1.1.7 proportions, we can calculate how we expect the number of genes detected to change with Ct value for wild-type SARS-CoV2.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>values</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span>,y<span class='o'>=</span><span class='nv'>..count..</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>geom_density</span><span class='o'>(</span>alpha<span class='o'>=</span><span class='m'>0.5</span>,position<span class='o'>=</span><span class='s'>"stack"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-20-1.png" width="700px" style="display: block; margin: auto;" />

</div>

This dataset gives us a sense of how much 'false positive B.1.1.7' we might see from random S dropout at a range of different Ct values (i.e. the `2` values might be predominantly this).

And we can even simulate what B.1.1.7 would look like under these conditions. We know that it is very rare to see the `S` positive unless `OR` and `N` are both also positive. So essentially B.1.1.7 SGTF would just turn `3` into `2` here.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>simul_b117</span> <span class='o'>=</span> <span class='nv'>values</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>genes_detected<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/ifelse.html'>ifelse</a></span><span class='o'>(</span><span class='nv'>genes_detected</span><span class='o'>==</span><span class='s'>"3"</span>,<span class='s'>"2"</span>,<span class='nv'>genes_detected</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>genes_detected</span><span class='o'>&gt;</span><span class='m'>0</span><span class='o'>)</span>


<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>simul_b117</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Ct</span>,fill<span class='o'>=</span><span class='nv'>genes_detected</span>,y<span class='o'>=</span><span class='nv'>..count..</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span>
  <span class='nf'>scale_fill_manual</span><span class='o'>(</span>values <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>, <span class='s'>"blue"</span>, <span class='s'>"black"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>geom_density</span><span class='o'>(</span>alpha<span class='o'>=</span><span class='m'>0.5</span>,position<span class='o'>=</span><span class='s'>"stack"</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-21-1.png" width="700px" style="display: block; margin: auto;" />

</div>

We can see that at high Ct, we see a lot of the single gene `OR` or `N`.

With the median Ct value around `31` in recent weeks, this can help to explain the presence of single gene positive samples that were previously called as `not-compatible with the new variant`.

<div class="highlight">

</div>

Conclusion
----------

We've been able to convert a valuable dataset generated by the ONS Infection Survey team into a machine-readable form, which permits insights into the apparent relative decline of B.1.1.7 in the recent report. Hopefully this can provide a useful building block in downstream analyses of Ct, gene dropout, and temporal dynamics.

<hr>

*If you enjoyed this exploration of how to extract data from vector-graphics in R, you might also want to see my [extraction of data from a rasterised chloropleth map](https://theosanderson.github.io/adhoc_covid/phe_graph/analysis.html).*

