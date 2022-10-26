---
title: EvenSampling
summary: The library used to select SARS-CoV-2 samples for sequencing in the United Kingdom
tags:
- covid
date: "2016-04-27T00:00:00Z"

# Optional external URL for project (replaces project detail page).
external_link: ""

image:
  caption: Photo by rawpixel on Unsplash
  focal_point: Smart

links:
url_code: ""
url_pdf: ""
url_slides: ""
url_video: ""
url_tool: ""

---

I was seconded to the Wellcome Sanger Institute at a time when almost every positive SARS-CoV-2 sample was being sent there for potential genomic surveillance. Sequencing capacity was capped at a certain number of samples per day, and I was tasked with developing an algorithm which would select plates for sequencing in order to:

- provide geographical coverage across local authorities such that the number of samples sequenced was proportional to the number of cases (despite sometimes biased arrival of samples to the lab)
- prioritise samples in key government priority areas such as those from care homes, and those from people arriving from abroad

I used OR-tools to find the optimal plate selection according to these priorities and constraints. The algorithm, called [evensampling](https://github.com/theosanderson/evensampling) was implemented in Python and was run daily with sample manifests from the latest samples to select the plates for cherry-picking each day. This algorithm was responsible for selecting millions of samples during the pandemic, and evolved in response to changing priorities. We showed that the balance of samples selected improved substantially after its implementation.