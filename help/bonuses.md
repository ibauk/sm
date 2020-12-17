## Bonuses: ordinary, special

Bonuses are the basic unit of scoring. Each rally will use its own mixture of bonuses, implementing each rally's unique design or geography. Usually, ordinary bonuses are used to represent normal geographical bonuses while specials are used for items such as sleep bonuses or 'flag at table' but as far as the software's concerned such distinctions don't matter and the rally can be specified using the more convenient bonus type.

Individual bonuses may be marked as *Compulsory* so that failure to score a compulsory bonus results in the entrant being DNF.

### Ordinary

Ordinary bonuses typically represent geographical locations that must be visited and each will have a fixed number of points associated with it. These are presented on the scorecard using their *bonusid* which will often be a two or three digit number. 

### Special

Specials are used for a variety of arbitrary bonuses (or indeed penalties). They are presented on the scorecard, separated from the ordinary bonuses, using their description rather than *bonusid* and can be grouped to use radio buttons rather than checkboxes. Specials may also be marked as *MustNot* so that scoring such a bonus results in DNF.

Specials may have a variable rather than fixed points value with the actual value being entered when scoring each entrant. They may also have a *multiplier* value when used in a [compound scoring](help:compound) scheme.

Specials can also be used to record rest or sleep period. This is used when calculating [overall average speed](help:speeding) and may be used to enforce rest period in longer rallies.