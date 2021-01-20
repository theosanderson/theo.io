---
title: How I stumbled upon a novel genome for a malaria-like parasite of primates
author: theo
type: post
date: 2018-04-23T17:39:10+00:00
url: /blog/2018/04/23/how-i-stumbled-upon-a-novel-genome-for-a-malaria-like-parasite-of-apes/
categories:
  - Uncategorized

---
<span style="color: gray;">Sometimes in science you come across things that are definitely interesting, and useful, but which you don&#8217;t have time to write up properly for one reason or another. I&#8217;m going to try to get into the habit of sharing these as blog-posts rather than letting them disappear. Here is one such story.</span>

* * *

Not long ago I was searching for orthologs of a malaria gene of interest on the NCBI non-redundant database, which allows one to search across the entire (sequenced) tree of life. Here is a recreation of what I saw:

<img class="alignnone size-full wp-image-121" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.27.51.png" alt="" width="780" height="236" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.27.51.png 780w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.27.51-300x91.png 300w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.27.51-768x232.png 768w" sizes="(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px" /> 

I was surprised to see that nestled among the _Plasmodium_ species was a sequence from a species called _Piliocolobus tephrosceles_.<!--more-->

A web search revealed this to be the linnaean name for the [Ugandan red colobus][1] monkey. Odd that this monkey should have a gene so similar to one from a parasite, eh? But there are some weird misannotations on NCBI. However a bit later I saw the same thing happen again with another gene, and thought something must be going on. I tried more malaria genes and found I could almost always find a strong match in the colobus assembly.

I navigated through NCBI to find some more about this particular monkey.

<img class="alignnone size-full wp-image-122" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.33.54.png" alt="" width="504" height="302" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.33.54.png 504w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.33.54-300x180.png 300w" sizes="(max-width: 504px) 85vw, 504px" /> 

[The page][2] tells us that this monkey from which this genome was sequenced was found here, in the depths of Kibale National Park (it was sequenced for a [University of Oregon][3] genome study, [in prep.][4]):

<img class="alignnone size-full wp-image-123" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.37.08.png" alt="" width="380" height="266" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.37.08.png 380w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.37.08-300x210.png 300w" sizes="(max-width: 380px) 85vw, 380px" /> 

It also tells us that the sequence came from whole blood. This methodology seemed to be consistent with researchers serendipitously acquiring sequence from a blood-borne parasite in the process of sequencing the monkey genome.

Just how much of this genome is there in the colobus sample? I downloaded the assembly to find out. The assembly was broken up into 47,644 contigs. My hypothesis was that some of these represented monkey sequence and others represented this sequence of a malaria-like parasite. I calculated the GC content of each contig and plotted a distribution:

<img class="alignnone size-full wp-image-124" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.55.22.png" alt="" width="398" height="298" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.55.22.png 398w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-21.55.22-300x225.png 300w" sizes="(max-width: 398px) 85vw, 398px" /> 

_Plasmodium spp._ have a much lower GC-content than mammalian species, and from this it looks like whatever species we have here shows this same bias. Just to be sure I also searched each &#8220;colobus&#8221; contig with Diamond (a faster Blast-like tool) against all protein sequences of the rhesus macaque and of _P. vivax_. If a contig had a significantly higher score in _P. vivax_ than rhesus it was assigned to that species and vice versa. Overlaying this data was pretty compelling:

<img class="alignnone size-full wp-image-125" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-22.07.52.png" alt="" width="754" height="317" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-22.07.52.png 754w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/Screen-Shot-2018-04-22-at-22.07.52-300x126.png 300w" sizes="(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px" /> 

I decided that by taking contigs which were either significantly better matches to _P. vivax_, or alternatively had a GC content below 24%, I would get a genome which was almost entirely this intriguing species. This gave me 13,460 contigs representing about 30 Mbp of sequence (_Plasmodium_ genomes are 20-30 Mbp so this could potentially be a genome&#8217;s worth &#8211; although I don&#8217;t always get BLAST matches, so I suspect it is somewhat less). I also got in touch with the person named in the NCBI accession to see if they were already investigating these:

<blockquote class="twitter-tweet" data-lang="en">
  <p dir="ltr" lang="en">
    hey <a href="https://twitter.com/theosanderson?ref_src=twsrc%5Etfw">@theosanderson</a> not specifically on the hepatocystis seqs in the assembly, though collaborators in our group have done some interesting work on hepatocystis in red colobus themselves <a href="https://t.co/yLs6ycTYrv">https://t.co/yLs6ycTYrv</a>
  </p>
  
  <p>
    — Noah Simons (@noahdsimons) <a href="https://twitter.com/noahdsimons/status/981977413314736128?ref_src=twsrc%5Etfw">April 5, 2018</a>
  </p>
</blockquote>



My home institute has released a rather neat web tool called [Companion][5], which allows you to upload a series of genomic contigs and have them annotated with reference to a known genome using a whole pipeline of analysis. (Unfortunately it only accepts up to 3,000 sequences. But you can download it as a Docker container to circumvent that restriction).

[Here][6] are the results from its run on these sequences. Companion identifies >2000 genes, including such favourites as circumsporozoite protein, GAP50, PTEX88 and the SERAs. It nicely annotates a pseudochromosome-based version of the genome (although I&#8217;m not sure it&#8217;s taking full advantage of the synteny at the moment).

So what is this mysterious species?

It clearly isn&#8217;t an already sequenced _Plasmodium_ species. A bit of googling suggested to me that it might be some species of _Hepatocystis_, which are [known to infect red Colobus][7]. Hepatocystis seems in some ways to be an unnecessary genus, since the latest phylogenies show that evolutionarily it lies nested within _Plasmodium,_ being more closely related to mammalian _Plasmodium_ species than is the avian malaria parasite _Plasmodium gallinaceum_. But there are reportedly important differences of approach between the groups &#8211; hepatocystis seems to lack the asexual cycle, with replication occurring only in the liver. One might hypothesise that this would lead to substantial loss of intra-erythrocytic genes. Also these beasts are transmitted by midges rather than mosquitoes (that data the result of a [decade][8]&#8211;[long][9] [search][10]). There are no other genome-scale sequencing datasets available for hepatocystis.

I searched GenBank for targeted hepatocystis sequences, and found a few, including the caseinolytic protease C gene which has been sequenced from numerous [bat][11] [isolates][12]. I found the matching sequences for these in the Colobus assembly and then Blasted those sequences back against GenBank to recover a set of sequences to analyse. Constructing a tree with [Phylogeny.fr][13] seemed to demonstrate clearly that this is some species of hepatocystis:

<img class="alignnone size-full wp-image-141" src="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/analysis-copy.png" alt="" width="709" height="1146" srcset="/post/archive_posts/old_wp_images/wp-content/uploads/2018/04/analysis-copy.png 709w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/analysis-copy-186x300.png 186w, /post/archive_posts/old_wp_images/wp-content/uploads/2018/04/analysis-copy-634x1024.png 634w" sizes="(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px" /> 

The closest relative found here is from a [_Cynopterus brachyotis_ bat from Singapore][14] &#8211; bats seem to pick up everything!

This is about as far as I&#8217;ve got with this. Some thoughts at this point:

  * If you are interested in what a specific gene looks like in hepatocystis it is likely that you can find at least part of it in these colobus contigs to compare to. It is very easy to run [a BLAST search at NCBI to check][15].
  * Early data-release is awesome! Parasitologists will really benefit from the University of Oregon primate researchers making this data available.
  * Someone should really see if we can get a really decent genome from these short read sequences. The library was prepared with Chromium technology and assembled with SuperNova, a 10X assembler. Is it possible these methods are optimised for diploid sequences and that assembling the raw data might enable a better parasite assembly? Or maybe the current assembly is pretty good &#8211; apart from the gaps &#8211; and missing _Plasmodium _genes are truly missing due to the differences in the hepatocystis lifecycle?
  * The depositors of the assembly might wish to partition it into its monkey and hepatocystis portions as I have above, lest a lot of parasite researchers are going to get confused when they find their parasite to be so closely related to a monkey 🙂 Happy to share my assignments!

 [1]: https://en.wikipedia.org/wiki/Ugandan_red_colobus
 [2]: https://www.ncbi.nlm.nih.gov/biosample/SAMN07735529/
 [3]: http://molecular-anthro.uoregon.edu/index.html
 [4]: https://twitter.com/MolecAnth/status/942838967342264320
 [5]: https://companion.sanger.ac.uk/
 [6]: https://companion.sanger.ac.uk/jobs/dcf60d31de2b43c841a62928
 [7]: https://www.ncbi.nlm.nih.gov/pmc/articles/PMC3676718/
 [8]: https://www.cabdirect.org/cabdirect/abstract/19522901249
 [9]: https://www.ncbi.nlm.nih.gov/pubmed/13443413
 [10]: https://www.ncbi.nlm.nih.gov/pubmed/13897017
 [11]: https://www.ncbi.nlm.nih.gov/pmc/articles/PMC5537238/
 [12]: https://www.ncbi.nlm.nih.gov/pubmed/18248741
 [13]: http://www.phylogeny.fr/
 [14]: https://www.ncbi.nlm.nih.gov/nuccore/eu254616.1
 [15]: https://www.ncbi.nlm.nih.gov/assembly/1441961