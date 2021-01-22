---
title: "Analysing Maccabi data on initial efficacy of first vaccine dose"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2021-01-22T22:18:37Z
lastmod: 2021-01-22T22:18:37Z
featured: false
draft: false
projects: []
mininote: true
output:  hugodown::md_document  
rmd_hash: 8409cd22e41da388

---

This exciting chart has emerged from the Maccabi study, providing evidence for efficacy of the Pfizer vaccine after the first dose.

![](chart.jpg)

As Eran Segal [pointed out on Twitter](https://twitter.com/segal_eran/status/1352696339377885187). The blue line actually includes the green line. I was interested to see what this would look like replotted as a Kaplan-Meier curve like the Pfizer graph.

![](pfizer.jpg)

<div class="highlight">

<pre class='chroma'><code class='language-r' data-lang='r'><span class='nv'>size_of_cohort</span> <span class='o'>=</span> <span class='m'>50777</span>
<span class='nv'>size_of_all_members</span> <span class='o'>=</span> <span class='m'>480000</span>
<span class='nv'>size_of_unvaccinated</span> <span class='o'>=</span> <span class='nv'>size_of_all_members</span><span class='o'>-</span><span class='nv'>size_of_cohort</span>
<span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>new_infections_all_members <span class='o'>=</span> <span class='nv'>AllMembersNormalised</span><span class='o'>*</span><span class='nv'>size_of_all_members</span><span class='o'>/</span><span class='nv'>size_of_cohort</span><span class='o'>)</span>
<span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>new_infections_unvaccinated <span class='o'>=</span> <span class='nv'>new_infections_all_members</span> <span class='o'>-</span> <span class='nv'>VaccinatedCohort</span><span class='o'>)</span>
<span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>daily_incidence_unvaccinated <span class='o'>=</span> <span class='nv'>new_infections_unvaccinated</span><span class='o'>/</span><span class='nv'>size_of_unvaccinated</span><span class='o'>)</span>
<span class='nv'>data</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>daily_incidence_vaccinated <span class='o'>=</span> <span class='nv'>VaccinatedCohort</span><span class='o'>/</span><span class='nv'>size_of_cohort</span><span class='o'>)</span>

<span class='nv'>graphdata</span> <span class='o'>=</span> <span class='nv'>data</span> <span class='o'>%&gt;%</span> <span class='nf'>select</span><span class='o'>(</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='nv'>Day</span>,<span class='nf'>contains</span><span class='o'>(</span><span class='s'>"daily_incidence"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>pivot_longer</span><span class='o'>(</span><span class='o'>-</span><span class='nv'>Day</span>,names_to<span class='o'>=</span><span class='s'>"group"</span>,values_to<span class='o'>=</span><span class='s'>"daily_incidence"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>separate</span><span class='o'>(</span><span class='nv'>group</span>,into<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"bla"</span>,<span class='s'>"bla2"</span>,<span class='s'>"condition"</span><span class='o'>)</span>,sep<span class='o'>=</span><span class='s'>"_"</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>select</span><span class='o'>(</span><span class='o'>-</span><span class='nf'>contains</span><span class='o'>(</span><span class='s'>"bla"</span><span class='o'>)</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>group_by</span><span class='o'>(</span><span class='nv'>condition</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>arrange</span><span class='o'>(</span><span class='nv'>Day</span><span class='o'>)</span> <span class='o'>%&gt;%</span> <span class='nf'>mutate</span><span class='o'>(</span>cumulative_incidence<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/cumsum.html'>cumsum</a></span><span class='o'>(</span><span class='nv'>daily_incidence</span><span class='o'>)</span><span class='o'>)</span>

<span class='nf'>ggplot</span><span class='o'>(</span><span class='nv'>graphdata</span>,<span class='nf'>aes</span><span class='o'>(</span>x<span class='o'>=</span><span class='nv'>Day</span>,y<span class='o'>=</span><span class='nv'>cumulative_incidence</span>,color<span class='o'>=</span><span class='nv'>condition</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_line</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>theme_classic</span><span class='o'>(</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>labs</span><span class='o'>(</span>caption<span class='o'>=</span><span class='s'>"Caveats: based on 7-day-moving average data so sharpness of start of initial efficacy will be understated."</span>,color<span class='o'>=</span><span class='s'>"Condition"</span>,y<span class='o'>=</span><span class='s'>"Cumulative Incidence"</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>scale_color_manual</span><span class='o'>(</span>values<span class='o'>=</span><span class='nf'><a href='https://rdrr.io/r/base/c.html'>c</a></span><span class='o'>(</span><span class='s'>"red"</span>,<span class='s'>"blue"</span><span class='o'>)</span><span class='o'>)</span><span class='o'>+</span><span class='nf'>geom_point</span><span class='o'>(</span><span class='o'>)</span>

</code></pre>
<img src="figs/unnamed-chunk-2-1.png" width="700px" style="display: block; margin: auto;" />

</div>

There are a lot of caveats about whether this is comparing like-for-like in terms of endpoint, and in some respects it is definitely not, and everything is smoothed out by the 7 day moving average. In addition, this is a much older age group where we'd expect the immune response to take longer.

In general the top plot is actually much more useful.

