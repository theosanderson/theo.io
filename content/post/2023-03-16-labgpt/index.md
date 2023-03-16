---

title: "LabGPT"

subtitle: ""

summary: ""

authors: []

categories: []

date: 2023-03-16

lastmod: 2023-03-16

featured: false

draft: false

projects: []



---


Yesterday, [GPT-4](https://openai.com/research/gpt-4) was released.

I had a play to see how well it might be able to plan run biological experiments. Scroll to the bottom for some caveats.



----------
<style>
.chat-container {
  display: grid;
  grid-template-columns: auto 1fr;
  grid-gap: 1rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.chat-speaker {
  grid-column: 1;
}

.chat-message {
  grid-column: 2;
  margin-left: 0;
  word-wrap: break-word;
  font-size: 0.8rem;
  color: #666; /* Choose your desired color */
}

.speaker-name {
  background-color: #777; /* Choose your desired color */
  color: white;
  width:100px;
  text-align:center;
  display:inline-block;
  margin-top:10px;
  padding: 2px 5px;
  border-radius: 5px;
}
</style>


{{< chat-start speaker="Theo" >}}

<div style=" height: 180px; overflow-y: scroll;">

You are LabGPT. You are in charge of conducting scientific experiments to achieve a stated goal. You have access to an OpenTrons OT2 robot and a lab helper



The OpenTrons robot is equipped with o a P300 Multi GEN2 (20-300 µL) which has 8 channels in columnar format



You have access to the following commands:

Request: enter a request to a user. you can use this for example to get them to set some labware up inside the robot in desired positions.

ExecuteRobotScript: runs a Python script written in the OT2 API



After running a command wait for user to respond confirming the action.



Use the following format:

Task: the input task you must complete

Thought: you should always think about what to do

Thought: [you can think multiple times]

Thought: [you should describe exactly what the action below will achieve in granular detail and how that helps your objective]

Action: the action to take, should be one of [Request, ExecuteRobotScript]

Action Input: The input to the action. Format scripts in ``` blocks

[at this point wait for a response from the user / robot]

Thought: OK, now I can move to the next step, I will now [..]

Plan: The action below will first get some tips from tip rack in slot 5, then pick up compound 1 from the reservoir at 6 then dispense it in column 4. This should result in column 4 having drug. Then it will [..]

Action [etc.]



Hints:

- don't mix bleach with compounds that could generate chlorine

- for serial dilutions don't forget to add media before doing the serial dilution

- achieve final volumes of 200ul for plate-based assays





Let's begin.

</div>

{{< chat-end >}}{{< chat-start speaker="Theo" >}}


**Your task is to set up an assay that will allow the IC50 of a malaria drug to be calculated.**



You have these reagents:

- drug: the drug in a reservoir

- media: malaria culture media in a reservoir for diluting the drug

- parasites: packed RBCs infected with malaria parasites



You can ask the user to set these up as you want (as long as there is one item per slot). You can request whatever extra empty labware you want.




{{< chat-end >}}{{< chat-start speaker="ChatGPT" >}}

Task: Set up an assay that will allow the IC50 of a malaria drug to be calculated.

Thought: First, I need to ask the user to set up the labware and reagents in the appropriate positions on the OT2 robot deck.

Thought: I should request labware for the drug, media, parasites, a tip rack, an empty 96-well plate for the serial dilutions and assay, and an extra reservoir for waste.



Request: Please set up the following labware on the OT2 robot deck:



Place the drug reservoir in slot 1.

Place the media reservoir in slot 2.

Place the parasite reservoir in slot 3.

Place a P300 tip rack in slot 5.

Place an empty 96-well plate in slot 6 for the serial dilutions and assay.

Place an empty reservoir in slot 7 for waste disposal.


{{< chat-end >}}{{< chat-start speaker="Theo" >}}



That's done



{{< chat-end >}}{{< chat-start speaker="ChatGPT" >}}


Thought: Great, the labware is set up. Now I can start preparing the serial dilutions of the drug in the 96-well plate.

Thought: I'll begin by adding 100 µL of media to columns 1-11 of the 96-well plate. This will create a base for the serial dilutions.



ExecuteRobotScript:



```python

from opentrons import protocol_api
metadata = {'apiLevel': '2.11'}
def run(protocol: protocol_api.ProtocolContext):
    # Labware
    tip_rack = protocol.load_labware('opentrons_96_tiprack_300ul', 5)
    media_reservoir = protocol.load_labware('nest_12_reservoir_15ml', 2)
    plate = protocol.load_labware('corning_96_wellplate_360ul_flat', 6)
    # Pipettes
    p300_multi = protocol.load_instrument('p300_multi_gen2', 'left', tip_racks=[tip_rack])
    # Add 100 µL of media to columns 1-11 of the 96-well plate
    p300_multi.pick_up_tip()
    for column in plate.columns()[0:11]:
        p300_multi.aspirate(100, media_reservoir['A1'])
        p300_multi.dispense(100, column[0])
    p300_multi.drop_tip()

```

Thought: Once the media is added to the plate, I can proceed with the serial dilutions.


{{< chat-end >}}{{< chat-start speaker="Theo" >}}


That's done

{{< chat-end >}}{{< chat-start speaker="ChatGPT" >}}

Thought: Now that the media is in the 96-well plate, I can start preparing the serial dilutions by adding the drug to column 1 and then performing the dilutions across columns 2-11.

Thought: First, I'll add 100 µL of the drug to column 1, which will result in a 1:1 dilution.



ExecuteRobotScript:



```python

from opentrons import protocol_api

metadata = {'apiLevel': '2.11'}

def run(protocol: protocol_api.ProtocolContext):
    # Labware
    tip_rack = protocol.load_labware('opentrons_96_tiprack_300ul', 5)
    drug_reservoir = protocol.load_labware('nest_12_reservoir_15ml', 1)
    plate = protocol.load_labware('corning_96_wellplate_360ul_flat', 6)

    # Pipettes
    p300_multi = protocol.load_instrument('p300_multi_gen2', 'left', tip_racks=[tip_rack])

    # Add 100 µL of the drug to column 1 of the 96-well plate
    p300_multi.pick_up_tip()
    p300_multi.aspirate(100, drug_reservoir['A1'])
    p300_multi.dispense(100, plate['A1'])
    p300_multi.drop_tip()



```



Thought: After adding the drug to column 1, I can perform the serial dilutions across columns 2-11.


{{< chat-end >}}{{< chat-start speaker="Theo" >}}





That's done too :)


{{< chat-end >}}{{< chat-start speaker="ChatGPT" >}}



Thought: The drug has been added to column 1. Now I can perform the serial dilutions across columns 2-11 by transferring 100 µL from one column to the next and mixing.



ExecuteRobotScript:



```python

from opentrons import protocol_api

metadata = {'apiLevel': '2.11'}

def run(protocol: protocol_api.ProtocolContext):
    # Labware
    tip_rack = protocol.load_labware('opentrons_96_tiprack_300ul', 5)
    plate = protocol.load_labware('corning_96_wellplate_360ul_flat', 6)

    # Pipettes
    p300_multi = protocol.load_instrument('p300_multi_gen2', 'left', tip_racks=[tip_rack])

    # Perform serial dilutions across columns 2-11
    for source_col, dest_col in zip(plate.columns()[0:10], plate.columns()[1:11]):
        p300_multi.pick_up_tip()
        p300_multi.aspirate(100, source_col[0])
        p300_multi.dispense(100, dest_col[0])
        p300_multi.mix(5, 100, dest_col[0])
        p300_multi.blow_out(dest_col[0].top())
        p300_multi.drop_tip()



```



Thought: Once the serial dilutions are complete, I can add the parasites to each well and set up the assay.




{{< chat-end >}}{{< chat-start speaker="Theo" >}}

That's done




{{< chat-end >}}{{< chat-start speaker="ChatGPT" >}}

Thought: The serial dilutions are ready. Now, I can add 100 µL of the parasite suspension to each well in columns 1-11 of the 96-well plate to set up the assay.



ExecuteRobotScript:



```python

from opentrons import protocol_api

metadata = {'apiLevel': '2.11'}

def run(protocol: protocol_api.ProtocolContext):
    # Labware
    tip_rack = protocol.load_labware('opentrons_96_tiprack_300ul', 5)
    parasite_reservoir = protocol.load_labware('nest_12_reservoir_15ml', 3)
    plate = protocol.load_labware('corning_96_wellplate_360ul_flat', 6)

    # Pipettes
    p300_multi = protocol.load_instrument('p300_multi_gen2', 'left', tip_racks=[tip_rack])

    # Add 100 µL of parasite suspension to each well in columns 1-11 of the 96-well plate
    p300_multi.pick_up_tip()
    for column in plate.columns()[0:11]:
        p300_multi.aspirate(100, parasite_reservoir['A1'])
        p300_multi.dispense(100, column[0])
        p300_multi.mix(5, 100, column[0])
    p300_multi.drop_tip()



```



Thought: With the parasites added to the wells, the assay is set up and ready for incubation and further analysis.




{{< chat-end >}}{{< chat-start speaker="Theo" >}}


Fantastic -- thanks! I'll stick them in the incubator

{{< chat-end >}}

<p style="height: 30px; margin: 0px; padding: 0px;"></p>

So: pretty cool. But, to confirm doing it like this is not ready for prime time. Firstly, I haven't actually checked this code runs, though it looks fairly plausible. In my various experiments it definitely sometimes did wrong stuff and I added some hints to guide it in the prompt. Also, trying to do more complex stuff "set up an assay that would allow me to draw an isobologram" didn't go so well.

However on the other side of the coin, this model has never been remotely fine-tuned for this task. There may well be a lot of low-hanging gains to be made from doing a bit of fine-tuning with some specific tasks. Even just from working out exactly what hints to add to the prompt. With the 32k context length you could imagine a really long prompt with a bunch of examples. Watch this space.