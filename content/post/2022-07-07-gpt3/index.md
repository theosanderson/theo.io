---
title: "Identifying malaria papers which report a phenotype with GPT-3"
subtitle: ""
summary: ""
authors: []
tags: [COVID, 'R notebook']
categories: []
date: 2021-01-21T17:58:37Z
lastmod: 2021-01-21T16:58:37Z
featured: false
draft: true
projects: []
mininote: true
output:  hugodown::md_document  
rmd_hash: d7a72f0c9624d793

---

Some years ago I launched [PhenoPlasm](http://phenoplasm.org) which aims to catalogue all reported phenotypes in Plasmodium in a systematic machine- (and human-) readable format. At the time I tried to populate it with all reported phenotypes in *Plasmodium falciparum* to date, using a series of searches on Google scholar. [RMgmDB](https://www.pberghei.eu/) pre-dates Phenoplasm, and provides an amazing curated resource for the rodent malaria databases.

Since then I have done my best to stay on top of the literature and add new phenotypes to the database as they appear. (Also, anyone can add a phenotype and I encourage you to!)

However it isn't always easy to stay on top of the literature, especially in recent years where I have been spending time on SARS-CoV-2 genetics. When the Bushell lab invited me to contribute to a [review](https://portlandpress.com/biochemsoctrans/article/doi/10.1042/BST20210281/231360/CRISPR-Cas9-and-genetic-screens-in-malaria) of the impact of screening in malaria parasites, by pulling out the latest phenotypes from the database, I felt I better do a big update sifting the literature again for phenotypes I might have missed in recent years, and previously. And I decided to do so with the help of machine learning.

I actually have quite a good training set. Each PhenoPlasm curated phenotype, and each RMgmDB modification, is associated with a Pubmed ID, so I had a list of several hundred Pubmed IDs, representing the sort of papers that I need to read to identify phenotypes. I don't have a list of definite negatives, but just identifying papers that come up in a search for "malaria OR plasmodium" and aren't in the positives produces a set that are almost all papers that are not of interest.

I decided to use a fine-tuned version of GPT-3 because having a model that started with a good knowledge of English would be helpful given that there weren't thousands of training examples. If the model really knows what words mean then it may be able to trained on examples that say "we deleted gene X" and "we disrupted gene X" and then generalise to examples it has not seen like "we abrogated the function of gene X".

The downside of using GPT-3 is that it is a commercial offering and you pay for each token (approximately per word) that you use. I have two pieces of data for each PubMed ID: the title and the abstract. I decided to take an approach very like a human who is limited in how many words they can use would take. I trained one model to classify papers into whether they would report a title just based on the title. Then I trained a second model to perform classification based on reading the full abstract.

If the cheap title model gave a reasonable probability that the paper might report a phenotype then I ran it through the abstract model to get more confidence one way or the other.

Because RMgmDB handles the rodent malaria phenotypes, I don't necessarily need to read those papers, so I also built in a classifier for whether the paper reports a phenotype in berghei or falciparum.

When we look at the 10 titles that the title classifier is most confident report a phenotype they look like this:

```
Plasmodium liver stage developmental arrest by depletion of a protein at the parasite-host interface.	
A Plasmodium homolog of ER tubule-forming proteins is required for parasite virulence.	
A role for the Plasmodium falciparum RESA protein in resistance against heat shock demonstrated using gene disruption.	
An essential role of the basal body protein SAS-6 in Plasmodium male gamete development and malaria transmission.	
Disruption of the Plasmodium falciparum PfPMT gene results in a complete loss of phosphatidylcholine biosynthesis via the serine-decarboxylase-phosphoethanolamine-methyltransferase pathway and severe growth and survival defects.	
Disruption of Plasmodium sporozoite transmission by depletion of sporozoite invasion-associated protein 1.	
A conserved malaria parasite antigen Pb22 plays a critical role in male gametogenesis in Plasmodium berghei.	
Analysis of mutant Plasmodium berghei parasites lacking expression of multiple PbCCp genes.	
Gene disruption reveals a dispensable role for plasmepsin VII in the Plasmodium berghei life cycle.	
Knockdown of the translocon protein EXP2 in Plasmodium falciparum reduces growth and protein export.
```

You can see that these titles are those with very clear flags of a phenotype (knockdown, essential, etc.)

In contrast the ten titles least likely to report a phenotype look like this:

```
A critical review of traditional medicine and traditional healer use for malaria and among people in malaria-endemic areas: contemporary research in low to middle-income Asia-Pacific countries.	
Co-infections of Schistosoma spp. and malaria with hepatitis viruses from endemic countries: A systematic review and meta-analysis.	
Same-day antiretroviral therapy initiation for people living with HIV who have tuberculosis symptoms: a systematic review.	
Efficacy and safety of artemisinin-based combination therapies for the treatment of uncomplicated malaria in pediatrics: a systematic review and meta-analysis.	
Aetiology of fever in returning travellers and migrants: a systematic review and meta-analysis.	
Self-diagnosis and self-treatment of Plasmodium spp. infection by travellers (1989-2019): A systematic review and meta-analysis.	
Non-malarial febrile illness: a systematic review of published aetiological studies and case reports from Africa, 1980-2015.	
Zoonotic causes of febrile illness in malaria endemic countries: a systematic review.	
Therapeutic efficacy of artesunate-amodiaquine and artemether-lumefantrine combinations for uncomplicated malaria in 10 sentinel sites across Ghana: 2015-2017.	
Malaria standby emergency treatment (SBET) for travellers visiting malaria endemic areas: a systematic review and meta-analysis.
```

These are papers with clear flags of relating to malaria in medicine, rather than molecular biology.

