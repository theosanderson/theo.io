---
title: "Identifying malaria phenotype papers with GPT-3"
subtitle: ""
summary: ""
authors: []
tags: [COVID, 'R notebook']
categories: []
date: 2022-08-11
lastmod: 2022-08-11
featured: false
draft: false
projects: []
mininote: true
output:  hugodown::md_document  
rmd_hash: d7a72f0c9624d793

---

**Visit [papers.phenoplasm.org](https://papers.phenoplasm.org)**

Some years ago I launched [PhenoPlasm](http://phenoplasm.org) which aims to catalogue all reported phenotypes in Plasmodium in a systematic machine- (and human-) readable format. At the time I tried to populate it with all reported phenotypes in *Plasmodium falciparum* to date, using a series of searches on Google scholar. [RMgmDB](https://www.pberghei.eu/) pre-dates Phenoplasm, and provides an amazing curated resource for the rodent malaria databases.

Since then I have done my best to stay on top of the literature and add new phenotypes to the database as they appear. (Also, anyone can add a phenotype, and I encourage you to!)

However it isn't always easy to stay on top of the literature, especially in recent years where I have been spending time on SARS-CoV-2 genetics. When the Bushell lab invited me to contribute to a [review](https://portlandpress.com/biochemsoctrans/article/doi/10.1042/BST20210281/231360/CRISPR-Cas9-and-genetic-screens-in-malaria) of the impact of screening in malaria parasites, by pulling out the latest phenotypes from the database, I felt I had better do a substantial update, sifting the literature again for phenotypes I might have missed in recent years. And I decided to do so with the help of machine learning.

I actually have quite a good training set. Each PhenoPlasm curated phenotype, and each [RMgmDB](https://www.pberghei.eu/index.php) entry, is associated with a Pubmed ID, so I had a list of several hundred Pubmed IDs, representing the sort of papers that I need to read to identify phenotypes. I don't have a list of definite negatives, but just identifying papers that come up in a search for "malaria OR plasmodium" and aren't in the positives produces a set that are almost all papers that are not of interest.

I decided to use a process called "fine-tuning" to train a model, where I'd start with a model (GPT-3) already trained to do other tasks in English, and then tweak it to be good at classifying papers according to whether they reported a phenotype. This is helpful because the model can already start with a good knowledge of English, which is helpful if there aren't thousands of positive training examples. If the model really knows what words mean then it may be able to trained on examples that say "we deleted gene X" and "we disrupted gene X", and then generalise to examples it has not seen like "we abrogated the function of gene X".

The downside of using GPT-3 is that it is a commercial offering and you pay for each token (approximately per word) that you use. I have two pieces of data for each PubMed ID: the title and the abstract. I decided to take an approach very like a human who is limited by how much time they can spend reading. I trained one model to classify papers by _just their title_. Then I trained a second model to perform classification based on reading the full abstract.

If the cheap title model gave a reasonable probability that the paper might report a phenotype then I ran it through the abstract model to get more confidence one way or the other.

Because RMgmDB handles the rodent malaria phenotypes, I don't necessarily need to read those papers, so I also built in a classifier for whether the paper reports a phenotype in _P. berghei_ or _P. falciparum_.

When we look at the 10 titles that the title classifier is most confident report a phenotype they look like this:


> 1. Plasmodium liver stage developmental arrest by depletion of a protein at the parasite-host interface.	
> 1. A Plasmodium homolog of ER tubule-forming proteins is required for parasite virulence.	
> 1. A role for the Plasmodium falciparum RESA protein in resistance against heat shock demonstrated using gene disruption.	
> 1. An essential role of the basal body protein SAS-6 in Plasmodium male gamete development and malaria transmission.	
> 1. Disruption of the Plasmodium falciparum PfPMT gene results in a complete loss of phosphatidylcholine biosynthesis via the serine-decarboxylase-phosphoethanolamine-methyltransferase pathway and severe growth and survival defects.	


You can see that these titles are those with very clear flags of a phenotype (knockdown, essential, etc.)

In contrast the ten titles least likely to report a phenotype look like this:

> 1. A critical review of traditional medicine and traditional healer use for malaria and among people in malaria-endemic areas: contemporary research in low to middle-income Asia-Pacific countries.	
> 1. Co-infections of Schistosoma spp. and malaria with hepatitis viruses from endemic countries: A systematic review and meta-analysis.	
> 1. Same-day antiretroviral therapy initiation for people living with HIV who have tuberculosis symptoms: a systematic review.	
> 1. Efficacy and safety of artemisinin-based combination therapies for the treatment of uncomplicated malaria in pediatrics: a systematic review and meta-analysis.	
> 1. Aetiology of fever in returning travellers and migrants: a systematic review and meta-analysis.	

These are papers with clear flags of relating to malaria in medicine, rather than molecular biology.

In general the model seems to be doing a decent job, and the abstract model is

I now run this model over the latest PubMed results each day, and the results are at [papers.phenoplasm.org](https://papers.phenoplasm.org)
