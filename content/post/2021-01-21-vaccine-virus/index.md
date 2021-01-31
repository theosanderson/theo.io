---
title: "The virus vs. the vaccine"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-01-21T16:58:37Z
lastmod: 2021-01-21T16:58:37Z
featured: false
draft: false
projects: []
mininote: true
output:  hugodown::md_document  
rmd_hash: e7c056d6413c1083

---

One framing for thinking about COVID is as a race between vaccination and infection, since we expect the former to have at least some mitigating effect on the latter. To get a sense for this I plot the cumulative infection estimates from the [MRC Biostatistics Nowcasting Reports](https://www.mrc-bsu.cam.ac.uk/tackling-covid-19/nowcasting-and-forecasting-of-covid-19/) against the vaccination numbers from [PHE](https://coronavirus.data.gov.uk/details/healthcare) (with historical data filled in from [Our World in Data](https://ourworldindata.org/covid-vaccinations)).

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='kr'><a href='https://rdrr.io/r/base/try.html'>try</a></span><span class='o'>(</span><span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://tidyverse.tidyverse.org'>tidyverse</a></span><span class='o'>)</span>, silent<span class='o'>=</span><span class='kc'>TRUE</span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='https://arxiv.org/abs/1403.2805'>jsonlite</a></span><span class='o'>)</span>
<span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://github.com/jrnold/ggthemes'>ggthemes</a></span><span class='o'>)</span>
<span class='nv'>a</span> <span class='o'>=</span> <span class='nf'><a href='https://rdrr.io/pkg/jsonlite/man/fromJSON.html'>fromJSON</a></span><span class='o'>(</span><span class='s'>"mrc_biostats.json"</span><span class='o'>)</span>
<span class='nv'>df</span><span class='o'>=</span><span class='nf'>tibble</span><span class='o'>(</span>date<span class='o'>=</span><span class='nv'>a</span><span class='o'>[[</span><span class='m'>1</span><span class='o'>]</span><span class='o'>]</span>,y<span class='o'>=</span><span class='nv'>a</span><span class='o'>[[</span><span class='m'>2</span><span class='o'>]</span><span class='o'>]</span><span class='o'>)</span>
<span class='nv'>df</span><span class='o'>$</span><span class='nv'>date</span><span class='o'>=</span><span class='nf'>lubridate</span><span class='nf'>::</span><span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>ymd</a></span><span class='o'>(</span><span class='nv'>df</span><span class='o'>$</span><span class='nv'>date</span><span class='o'>)</span>


<span class='nv'>df</span><span class='o'>$</span><span class='nv'>label</span> <span class='o'>=</span> <span class='s'>"People infected"</span>
<span class='nv'>b</span> <span class='o'>=</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='s'>"vaccinations.csv"</span><span class='o'>)</span>
<span class='nv'>b</span><span class='o'>$</span><span class='nv'>y</span><span class='o'>=</span><span class='nv'>b</span><span class='o'>$</span><span class='nv'>numFirstDose</span>
<span class='nv'>b</span><span class='o'>$</span><span class='nv'>date</span><span class='o'>=</span><span class='nf'>lubridate</span><span class='nf'>::</span><span class='nf'><a href='http://lubridate.tidyverse.org/reference/ymd.html'>dmy</a></span><span class='o'>(</span><span class='nv'>b</span><span class='o'>$</span><span class='nv'>date</span><span class='o'>)</span>
<span class='nv'>b</span><span class='o'>$</span><span class='nv'>label</span> <span class='o'>=</span> <span class='s'>"People vaccinated"</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>df</span>,<span class='nv'>b</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>date</span>,y<span class='o'>=</span><span class='nv'>y</span><span class='o'>/</span><span class='m'>1000000</span>,color<span class='o'>=</span><span class='nv'>label</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span>size<span class='o'>=</span><span class='m'>1</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Date"</span>,y<span class='o'>=</span><span class='s'>"Number of people (millions)"</span>,color<span class='o'>=</span><span class='s'>"Type"</span>,title<span class='o'>=</span><span class='s'>"Infection and vaccination levels in England"</span>,caption<span class='o'>=</span><span class='s'>"Data sources: MRC Biostatistics Unit (cumulative infection estimates)\nOur World In Data &amp; PHE (vaccination figures – at least one dose)"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'><a href='https://rdrr.io/pkg/ggthemes/man/theme_hc.html'>theme_hc</a></span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span>+
  <span class='nf'>theme</span><span class='o'>(</span>plot.title <span class='o'>=</span> <span class='nf'>element_text</span><span class='o'>(</span>hjust <span class='o'>=</span> <span class='m'>0.5</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-1-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'><span class='nf'>ggsave</span><span class='o'>(</span><span class='s'>"plot.png"</span>,width<span class='o'>=</span><span class='m'>7.3</span>,height<span class='o'>=</span><span class='m'>4</span>, type <span class='o'>=</span> <span class='s'>"cairo"</span><span class='o'>)</span> 
</code></pre>

</div>

If we literally were to imagine vaccination and infection to be mutually exclusive (which clearly isn't true in either direction), we could plot a graph like this to look at where they might meet.

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>

<span class='nv'>c</span><span class='o'>=</span><span class='nv'>b</span>
<span class='nv'>c</span><span class='o'>$</span><span class='nv'>label</span> <span class='o'>=</span> <span class='s'>"People unvaccinated"</span>
<span class='nv'>c</span><span class='o'>$</span><span class='nv'>y</span><span class='o'>=</span><span class='m'>56000000</span><span class='o'>-</span><span class='nv'>c</span><span class='o'>$</span><span class='nv'>y</span>
<span class='nf'>ggplot</span><span class='o'>(</span><span class='nf'>bind_rows</span><span class='o'>(</span><span class='nv'>df</span>,<span class='nv'>c</span><span class='o'>)</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>date</span>,y<span class='o'>=</span><span class='nv'>y</span><span class='o'>/</span><span class='m'>1000000</span>,color<span class='o'>=</span><span class='nv'>label</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span>size<span class='o'>=</span><span class='m'>1</span><span class='o'>)</span> <span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Date"</span>,y<span class='o'>=</span><span class='s'>"Number of people (millions)"</span>,color<span class='o'>=</span><span class='s'>"Type"</span>,title<span class='o'>=</span><span class='s'>"Infection and vaccination levels in England"</span>,caption<span class='o'>=</span><span class='s'>"Data sources: MRC Biostatistics Unit (cumulative infection estimates)\nOur World In Data &amp; PHE (vaccination figures – at least one dose)"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'><a href='https://rdrr.io/pkg/ggthemes/man/theme_hc.html'>theme_hc</a></span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span>+
  <span class='nf'>theme</span><span class='o'>(</span>plot.title <span class='o'>=</span> <span class='nf'>element_text</span><span class='o'>(</span>hjust <span class='o'>=</span> <span class='m'>0.5</span><span class='o'>)</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-2-1.png" width="700px" style="display: block; margin: auto;" />
<pre class='chroma'><code class='language-r' data-lang='r'>

<span class='nf'>ggsave</span><span class='o'>(</span><span class='s'>"plot2.png"</span>,width<span class='o'>=</span><span class='m'>7.3</span>,height<span class='o'>=</span><span class='m'>4</span>, type <span class='o'>=</span> <span class='s'>"cairo"</span><span class='o'>)</span> 
</code></pre>

</div>

