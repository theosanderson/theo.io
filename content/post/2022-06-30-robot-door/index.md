---
title: "Another door opens"
subtitle: ""
summary: ""
authors: []
tags: []
categories: []
date: 2022-06-10
lastmod: 2022-06-10
featured: false
draft: true
projects: []
mininote: true
output:  hugodown::md_document  
---

Something I have learnt after having this blog a while is that posts on surprisingly niche topics do eventually find quite a few readers (e.g. several people I know in real life have stumbled on my [epMotion 5075 teardown](https://theo.io/blog/2019/01/05/emotion-5075-teardown/) after encountering such a robot).

In that spirit, I am writing the first of what may be a series of dives into various aspects of the Open Robotic Incubator that forms a key part of the PlasmoTron project. And today, we are going to talk about the door.

Keeping malaria parasites happy in the short term involves: keeping them at the right temperature, and keeping them in the right gas mixture. The realisation that parasites require reduced oxygen concentration to grow was perhaps the most fundamental discovery required for the establishment of malaria culture in 1976.

Maintaining temperature, and especially gas concentration, requires a relatively strict isolation between the parasites and the outside laboratory, and that requires a door.

I have not been able to find designs for an open hardware door that forms a reasonable gas tight seal, and so I developed my own.

It's pretty simple! A doorframe, and a swiveling door mounted to it. To make a good seal, we use a gasket of 3mm neoprene foam which we leave space for. We also increase the stepper motor's torque significantly with a gear train. The motor shaft mounts to a cog (orange) with 9 teeth using a grub-screw. This cog drives a cog with 18 teeth for a 2:1 reduction. A coupled gear with 7 teeth drives a gear (purple) with 25 teeth, for a.. 25:7 reduction. Finally a gear with 9 teeth (purple) drives a gear on the door (red) with 25 teeth, for a 25:9 reduction. In all this is a 1250:81 reduction, so increases torque by about 15x.