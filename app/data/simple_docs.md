language: "en"

pipeline:
- name: "nlp_spacy"
  model: "en"
- name: "tokenizer_spacy"
- name: "ner_crf"
- name: "intent_featurizer_spacy"
- name: "intent_classifier_sklearn"


data: |
    ## intent:belittling_text
    - You sould [simply](vocabulary) run.
    - It is quite [easy](vocabulary) to do.
    - You should [just] install.
    - A [quick](vocabulary) look and it [is clear](vocabulary)
    - [simply](vocabulary) use a
    - it is [quite](vocabulary)
    - it is a [trivial] task to do
    - If you [simply](vocabulary) want to use
    - and [of cource](vocabulary) it is cool
  

    ## lookup:vocabulary
    - simply
    - simple
    - just
    - easy
    - easily
    - quick
    - "of  course"
    - logically
    - clearly
    - obviously
    - merely
    - basically
    - trivial

    ## regex:vocabulary
    - (simply|simple|just|easy|easily|quick|of  course|logically|clearly|obviously|merely|basically|trivial)
