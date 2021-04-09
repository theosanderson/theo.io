---
title: "LFD, PCR, PPV, TLA"
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
rmd_hash: 6edbf835c715e5ce

---

People are very interested in the *positive predictive value* of lateral flow tests. That is, if you receive a positive result from the test, how likely is it that you truly are infected. Personally I think that in terms of measuring whether these tests are helpful, this metric is actually not terribly useful - since it is strongly affected by how much virus is circulating in the population. That means that the metric suggests that lateral flow testing is useless in countries with very low numbers of cases. But actually LFDs have exactly the same effect on R whatever the level of virus in the population, and the same number of absolute false positives.

Nevertheless, it is understandable that people want a test to ideally give them the right answer about their own status. This is helped in part by the fact that the government now says that all LFD positives should be followed up by confirmatory PCR testing, which both helps an individual by confirming their status, and also has the potential to provide us all with information about the reliability of these tests. Information has not been released systematically on what proportion of PCR retests confirm LFD results, but today the coronavirus dashboard has changed to exclude these results. Therefore by comparing today's results with yesterdays we can get a sense of this metric and calculate the minimum possible PPV, assuming that the PCR retest has 100% sensitivity. These sorts of figures have been described by [Oliver Johnson](https://twitter.com/BristOliver/status/1380544543695716353) and [Alex Selby](https://twitter.com/alexselby1770/status/1380614571791151106).

Here is my quick look in R

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'>

<span class='nv'>before</span> <span class='o'>=</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='s'>"https://api.coronavirus.data.gov.uk/v2/data?areaType=nation&amp;areaCode=E92000001&amp;metric=newCasesLFDOnlyBySpecimenDate&amp;metric=changeInNewCasesBySpecimenDate&amp;format=csv&amp;release=2021-04-08&amp;metric=newCasesLFDConfirmedPCRBySpecimenDate"</span><span class='o'>)</span>

<span class='c'>#&gt; Parsed with column specification:</span>
<span class='c'>#&gt; cols(</span>
<span class='c'>#&gt;   date = <span style='color: #0000BB;'>col_date(format = "")</span><span>,</span></span>
<span class='c'>#&gt;   areaType = <span style='color: #BB0000;'>col_character()</span><span>,</span></span>
<span class='c'>#&gt;   areaCode = <span style='color: #BB0000;'>col_character()</span><span>,</span></span>
<span class='c'>#&gt;   areaName = <span style='color: #BB0000;'>col_character()</span><span>,</span></span>
<span class='c'>#&gt;   newCasesLFDOnlyBySpecimenDate = <span style='color: #00BB00;'>col_double()</span><span>,</span></span>
<span class='c'>#&gt;   changeInNewCasesBySpecimenDate = <span style='color: #00BB00;'>col_double()</span><span>,</span></span>
<span class='c'>#&gt;   newCasesLFDConfirmedPCRBySpecimenDate = <span style='color: #00BB00;'>col_double()</span></span>
<span class='c'>#&gt; )</span>


<span class='nv'>after</span> <span class='o'>=</span> <span class='nf'>read_csv</span><span class='o'>(</span><span class='s'>"https://api.coronavirus.data.gov.uk/v2/data?areaType=nation&amp;areaCode=E92000001&amp;metric=newCasesLFDOnlyBySpecimenDate&amp;metric=changeInNewCasesBySpecimenDate&amp;format=csv&amp;release=2021-04-09&amp;metric=newCasesLFDConfirmedPCRBySpecimenDate"</span><span class='o'>)</span>

<span class='c'>#&gt; Parsed with column specification:</span>
<span class='c'>#&gt; cols(</span>
<span class='c'>#&gt;   date = <span style='color: #0000BB;'>col_date(format = "")</span><span>,</span></span>
<span class='c'>#&gt;   areaType = <span style='color: #BB0000;'>col_character()</span><span>,</span></span>
<span class='c'>#&gt;   areaCode = <span style='color: #BB0000;'>col_character()</span><span>,</span></span>
<span class='c'>#&gt;   areaName = <span style='color: #BB0000;'>col_character()</span><span>,</span></span>
<span class='c'>#&gt;   newCasesLFDOnlyBySpecimenDate = <span style='color: #00BB00;'>col_double()</span><span>,</span></span>
<span class='c'>#&gt;   changeInNewCasesBySpecimenDate = <span style='color: #00BB00;'>col_double()</span><span>,</span></span>
<span class='c'>#&gt;   newCasesLFDConfirmedPCRBySpecimenDate = <span style='color: #00BB00;'>col_double()</span></span>
<span class='c'>#&gt; )</span>
</code></pre>

</div>

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='kr'><a href='https://rdrr.io/r/base/library.html'>library</a></span><span class='o'>(</span><span class='nv'><a href='http://zoo.R-Forge.R-project.org/'>zoo</a></span><span class='o'>)</span>

<span class='c'>#&gt; </span>
<span class='c'>#&gt; Attaching package: 'zoo'</span>

<span class='c'>#&gt; The following objects are masked from 'package:base':</span>
<span class='c'>#&gt; </span>
<span class='c'>#&gt;     as.Date, as.Date.numeric</span>

<span class='nv'>both</span> <span class='o'>=</span> <span class='nf'>inner_join</span><span class='o'>(</span><span class='nv'>before</span>,<span class='nv'>after</span>,by<span class='o'>=</span><span class='s'>"date"</span>, suffix<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"_before"</span>,<span class='s'>"_after"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>date</span><span class='o'>&gt;</span><span class='s'>"2020-12-15"</span><span class='o'>)</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>date</span><span class='o'>&lt;</span><span class='s'>"2021-04-01"</span><span class='o'>)</span>

<span class='nv'>both</span><span class='o'>=</span> <span class='nv'>both</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>notional_false_positives <span class='o'>=</span> <span class='nv'>newCasesLFDOnlyBySpecimenDate_before</span> <span class='o'>-</span> <span class='nv'>newCasesLFDOnlyBySpecimenDate_after</span> <span class='o'>-</span>  <span class='o'>(</span><span class='nv'>newCasesLFDConfirmedPCRBySpecimenDate_before</span> <span class='o'>-</span> <span class='nv'>newCasesLFDConfirmedPCRBySpecimenDate_after</span><span class='o'>)</span> <span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>arrange</span><span class='o'>(</span><span class='nv'>date</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>notional_false_positives<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/pkg/zoo/man/rollmean.html'>rollsum</a></span><span class='o'>(</span><span class='nv'>notional_false_positives</span>,<span class='m'>7</span>,na.pad<span class='o'>=</span><span class='kc'>T</span><span class='o'>)</span>,newCasesLFDConfirmedPCRBySpecimenDate_after<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/pkg/zoo/man/rollmean.html'>rollsum</a></span><span class='o'>(</span><span class='nv'>newCasesLFDConfirmedPCRBySpecimenDate_after</span>,<span class='m'>7</span>,na.pad<span class='o'>=</span><span class='kc'>T</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span> notional_proportion_of_positives_false <span class='o'>=</span> <span class='nv'>notional_false_positives</span> <span class='o'>/</span> <span class='o'>(</span><span class='nv'>notional_false_positives</span><span class='o'>+</span> <span class='nv'>newCasesLFDConfirmedPCRBySpecimenDate_after</span> <span class='o'>)</span> <span class='o'>)</span><span class='o'>%&gt;%</span> <span class='nf'><a href='https://rdrr.io/r/stats/filter.html'>filter</a></span><span class='o'>(</span><span class='nv'>notional_false_positives</span><span class='o'>&gt;</span><span class='m'>0</span><span class='o'>)</span>

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>both</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>date</span>,y<span class='o'>=</span><span class='m'>1</span><span class='o'>-</span><span class='nv'>notional_proportion_of_positives_false</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_point</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_smooth</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>coord_cartesian</span><span class='o'>(</span>ylim<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='m'>0</span>,<span class='m'>1</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>x<span class='o'>=</span><span class='s'>"Day"</span>,y<span class='o'>=</span><span class='s'>"Notional minimum positive predictive value"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_y_continuous</span><span class='o'>(</span>label<span class='o'>=</span><span class='nf'>scales</span><span class='nf'>::</span><span class='nv'><a href='https://scales.r-lib.org/reference/label_percent.html'>percent</a></span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_bw</span><span class='o'>(</span><span class='o'>)</span> 

<span class='c'>#&gt; `geom_smooth()` using method = 'loess' and formula 'y ~ x'</span>

</code></pre>
<img src="figs/unnamed-chunk-4-1.png" width="700px" style="display: block; margin: auto;" />

</div>

