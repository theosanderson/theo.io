---
title: "Investigating the latest round of REACT"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-01-21T17:58:37Z
lastmod: 2021-01-21T16:58:37Z
featured: false
draft: false
projects: []
mininote: true
output:  hugodown::md_document  
rmd_hash: d7a72f0c9624d793

---

The [latest round of the REACT study](https://spiral.imperial.ac.uk/handle/10044/1/85583) has come out, and been quite controversial. Let's examine some of the raw data behind it.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://tidyverse.tidyverse.org'>tidyverse</a></span><span class='o'>)</span>
</code></pre>

</div>

Let's download the REACT data and process it into a nice format:

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>positive</span> <span class='o'>&lt;-</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/connections.html'>url</a></span><span class='o'>(</span><span class='s'>"https://raw.githubusercontent.com/mrc-ide/reactidd/master/inst/extdata/positive.csv"</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Missing column names filled in: 'X1' [1]</span>

<span class='nv'>total</span> <span class='o'>&lt;-</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/connections.html'>url</a></span><span class='o'>(</span><span class='s'>"https://raw.githubusercontent.com/mrc-ide/reactidd/master/inst/extdata/total.csv"</span><span class='o'>)</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Missing column names filled in: 'X1' [1]</span>
</code></pre>

</div>

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>positive</span><span class='o'>$</span><span class='nv'>type</span> <span class='o'>=</span> <span class='s'>"positive"</span>
<span class='nv'>total</span><span class='o'>$</span><span class='nv'>type</span> <span class='o'>=</span> <span class='s'>"total"</span>
<span class='nv'>all</span> <span class='o'>=</span> <span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>positive</span>, <span class='nv'>total</span><span class='o'>)</span>
<span class='nf'><a href='https://rdrr.io/r/base/colnames.html'>colnames</a></span><span class='o'>(</span><span class='nv'>all</span><span class='o'>)</span><span class='o'>[</span><span class='m'>1</span><span class='o'>]</span> <span class='o'>=</span> <span class='s'>"Date"</span>

<span class='nv'>all</span> <span class='o'>=</span> <span class='nv'>all</span> <span class='o'>%&gt;%</span> <span class='nf'>pivot_longer</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='o'>-</span><span class='nv'>Date</span>,<span class='o'>-</span><span class='nv'>type</span><span class='o'>)</span>,names_to<span class='o'>=</span><span class='s'>"Region"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>pivot_wider</span><span class='o'>(</span>names_from<span class='o'>=</span><span class='nv'>type</span><span class='o'>)</span>
<span class='nv'>all</span>

<span class='c'>#&gt; <span style='color: #555555;'># A tibble: 1,485 x 4</span></span>
<span class='c'>#&gt;    Date       Region                   positive total</span>
<span class='c'>#&gt;    <span style='color: #555555;font-style: italic;'>&lt;date&gt;</span><span>     </span><span style='color: #555555;font-style: italic;'>&lt;chr&gt;</span><span>                       </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span><span> </span><span style='color: #555555;font-style: italic;'>&lt;dbl&gt;</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 1</span><span> 2020-05-05 South East                      0   113</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 2</span><span> 2020-05-05 North East                      0    13</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 3</span><span> 2020-05-05 North West                      1    73</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 4</span><span> 2020-05-05 Yorkshire and The Humber        0    33</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 5</span><span> 2020-05-05 East Midlands                   1    77</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 6</span><span> 2020-05-05 West Midlands                   0    51</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 7</span><span> 2020-05-05 East of England                 0    93</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 8</span><span> 2020-05-05 London                          0    39</span></span>
<span class='c'>#&gt; <span style='color: #555555;'> 9</span><span> 2020-05-05 South West                      0    42</span></span>
<span class='c'>#&gt; <span style='color: #555555;'>10</span><span> 2020-05-06 South East                      0    62</span></span>
<span class='c'>#&gt; <span style='color: #555555;'># … with 1,475 more rows</span></span>m
</code></pre>

</div>

Now we can calculate binomial confidence intervals by location for each date and plot them.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>all</span> <span class='o'>=</span> <span class='nv'>all</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='o'>!</span><span class='nf'><a href='https://rdrr.io/r/base/NA.html'>is.na</a></span><span class='o'>(</span><span class='nv'>positive</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='o'>!</span><span class='nf'><a href='https://rdrr.io/r/base/NA.html'>is.na</a></span><span class='o'>(</span><span class='nv'>total</span><span class='o'>)</span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'>binom</span><span class='o'>)</span>

<span class='nv'>cis</span> <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/pkg/binom/man/binom.confint.html'>binom.confint</a></span><span class='o'>(</span><span class='nv'>all</span><span class='o'>$</span><span class='nv'>positive</span>,<span class='nv'>all</span><span class='o'>$</span><span class='nv'>total</span>, method<span class='o'>=</span><span class='s'>"exact"</span><span class='o'>)</span>


<span class='nv'>all</span><span class='o'>$</span><span class='nv'>lower</span><span class='o'>=</span><span class='nv'>cis</span><span class='o'>$</span><span class='nv'>lower</span>
<span class='nv'>all</span><span class='o'>$</span><span class='nv'>mean</span> <span class='o'>=</span> <span class='nv'>cis</span><span class='o'>$</span><span class='nv'>mean</span>
<span class='nv'>all</span><span class='o'>$</span><span class='nv'>upper</span><span class='o'>=</span><span class='nv'>cis</span><span class='o'>$</span><span class='nv'>upper</span>

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>all</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>Date</span><span class='o'>&gt;</span><span class='s'>"2020-12-15"</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Date</span>,ymin<span class='o'>=</span><span class='nv'>lower</span>,ymax<span class='o'>=</span><span class='nv'>upper</span>,y<span class='o'>=</span><span class='nv'>mean</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_pointrange</span><span class='o'>(</span>color<span class='o'>=</span><span class='s'>"black"</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>facet_wrap</span><span class='o'>(</span><span class='o'>~</span><span class='nv'>Region</span>,scales<span class='o'>=</span><span class='s'>"free_y"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_log10</span><span class='o'>(</span>labels <span class='o'>=</span> <span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org//reference/label_percent.html'>percent</a></span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span> <span class='o'>+</span> <span class='nf'>labs</span><span class='o'>(</span>y<span class='o'>=</span><span class='s'>"Probability of testing positive"</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Transformation introduced infinite values in continuous y-axis</span>

<span class='c'>#&gt; Warning: Transformation introduced infinite values in continuous y-axis</span>

</code></pre>
<img src="figs/unnamed-chunk-4-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>

<span class='nf'>ggsave</span><span class='o'>(</span><span class='s'>"plot.png"</span>,width<span class='o'>=</span><span class='m'>9</span>,height<span class='o'>=</span><span class='m'>5</span>, type<span class='o'>=</span><span class='s'>"cairo"</span><span class='o'>)</span>

<span class='c'>#&gt; Warning: Transformation introduced infinite values in continuous y-axis</span>

<span class='c'>#&gt; Warning: Transformation introduced infinite values in continuous y-axis</span>
</code></pre>

</div>

This looks to accord well with the table of R values that REACT provide in Table 3: ![](react-table.png)

This isn't especially surprising, but I think the visualisation helps to make sense of how these values came to be calculated (even though they are probably based on a more complex analysis weighting for various demographic factors).

That's it for now.

