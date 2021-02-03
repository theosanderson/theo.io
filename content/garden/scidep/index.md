
---
title: "Scidep - a dependency tree for scientific discovery"
subtitle: ""
summary: ""
authors: []


---

#### The problem

<blockquote>``It has not escaped our notice that the specific pairing we have postulated immediately suggests a possible copying mechanism for the genetic material.''</blockquote>

Thus Watson and Crick concluded their short article revealing the structure of DNA in 1953. In writing this they were part of a scientific tradition that is alive and well to this day, the fear that others may get the credit for trivial extensions of one's own hard work.

Despite huge changes in the landscape of scientific research since, this fear is as alive as ever. And in many circumstances, it may be justified. Scientists applying for jobs and funding are assessed on their publication records, and publications, especially in "top" journals often rely on narratives and mechanisms, rather than raw data.

Yet disseminating data by waiting for publication in journals, or even for the writing of narrative manuscripts would, and does, drastically reduce scientific progress. An especially dramatic example of this was the release of the first SARS-CoV2 genome on a Discourse group on January 10 by Yong-Zhen Zhang, via Eddie Holmes. This genome set in train the development of PCR tests and vaccines (Moderna's had been designed just two days later). Each day of delay in posting this sequence would have led at minimum to tens of thousands of additional lives lost, considering vaccination alone.

In many ways the pandemic has brought out the best in the scientific community, with many sharing data [in real time on Twitter](Menachery), and elsewhere, without  worrying too much about being "scooped". A shining light has been the sharing of sequence data, deposited on the GISAID database, and made explorable in real time by initiatives such as Nextstrain.

But this data sharing has not always been completely smooth. Sometimes those who have done the hard, expensive, work required to generate these sequence data have complained that others have published relatively trivial analyses of these data without involving these originators.

These complainants have a point. The GISAID terms and conditions, accepted by anyone in order to access the data, require acknowledgement of the originating laboratories in any analyses of their data. They also require 'best efforts' be made to collaborate with these laboratories, though the FAQ explicitly states that permission from the originating laboratories is not required for publications.


Fundamentally, the argument that many critics are making is that the originators of the data should have the right to make the first narrative descriptions of the events that these data describe: the emergence of a particular variant, its spread in a new country. I suspect that at the root of this argument is a valid fear, that more prestige and recognition may be associated with the statistical description.

But what's the point of sharing data in real time on a platform like GISAID? Ultimately it has to be to allow others to analyse it. And those analyses are not of any use unless they are communicated. And the traditional way in which scientific analyses are communicated is by publishing them in journals (I think more efficient alternatives may exist, but this is the current system).

The use of GISAID requires acknowledgements, which at least some of these criticised papers complied wtih, providing a table of all the laboratories contributing sequences. But this wasn't enough to assuage the concerns. Because being listed in the acknowledgements of a paper describing an important phenomenon doesn't confer the same prestige as having written that paper. Fundamentally this is the problem. Someone can generate the data needed to understand a key scientific issue, deposit it, and not get the credit for the insights it leads to.

#### A remedy?

How can we incentivise scientists to create and deposit valuable data? In my view by recording the use of that data, and giving them credit. When another group publishes the first analysis of a set of valuable data, their paper will accrue citations. I think that isn't in itself a problem, and isn't really what causes the resentment. The problem is that those citations, valuable scientific prestige, occur at the expense of the originators of the data. 

What if those citations were transitive -- if they flowed from the analysis paper straight down to the originators of the data? The original researchers would gain kudos from there being more analyses of their data out in the world.

This is is in essence the notion of a dependency tree. An analysis of SARS-CoV2 genome sequences has many dependencies: the originators of the sequences, the bioinformatic tools for analysing them. Those dependencies each in turn have their own dependencies (generation of a genome may rely on a sequencing protocols like ARTIC).

Fundamentally, I think that if we recorded these dependencies, we would incentivise a better sort of science. Dependencies are different from citations in that they are not limited in number, do not have to be associated with a particular sentence, and are not limited to pointing to, or featuring in publications.

#### Implementation

*SciDep* would be a dependency tree of scientific contributions. It would allow the creation of unique IDs for any object desired:
- Publications
- Repositories
- 


******
scratchpad


Proposal: GISAID (and other databases) provide a unique ID (rather like a DOI) for each genome / datapoint. scidep.gisaid.11154 . GISAID mandates that you can use that data, but only if you create an entity for your paper scidep.pubmed.115343 which includes as dependencies each of the genomes you used in your analysis.

The supercharged version is that the license requires that you apply the same feature to your paper. If you, or a publisher, want to cite it, they in turn have to create a scidep entity for their publication
