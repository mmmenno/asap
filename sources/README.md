# ASAP Sources

## BAG buildings construction years

The Basisadministratie Adressen en Gebouwen (BAG) contains every building in the Netherlands. The construction dates of buildings are not always very thrustworthy - lots of buildings are said to be built in 1005, meaning, I guess, something like "probably rather old".

The BAG can be accessed through a [SPARQL endpoint](https://data.pdok.nl/sparql#), in which we used the following query to extract the [oldest building of each street](BAG-oldest-building-of-street.csv):

```
PREFIX bag: <http://bag.basisregistraties.overheid.nl/def/bag#>
select DISTINCT ?street ?straatnaam (MIN(?bouwjaar) AS ?minbouwjaar)
where {
  ?na bag:bijbehorendeOpenbareRuimte ?street .
  ?street bag:naamOpenbareRuimte ?straatnaam .
  ?street bag:bijbehorendeWoonplaats <http://bag.basisregistraties.overheid.nl/bag/id/woonplaats/3594> .
  ?vo bag:hoofdadres ?na .
  ?vo bag:pandrelatering ?pand .
  ?pand bag:oorspronkelijkBouwjaar ?bouwjaar .
  FILTER (?bouwjaar > 1005)
}
GROUP BY ?street ?straatnaam
```
Expectations are the BAG construction years will be especially useful to date more recent streets that lack raadsbesluiten.


## Raadsbesluiten

Coming soon.

## Transportakten

Coming soon.

# Maps

When a street appears on a map, and it's not a planners map, we can safely assume the street existed when the map was created.

## Jodocus Hondius Map 1612

[The Hondius Map](https://beeldbank.amsterdam.nl/afbeelding/010001000605) lacks the accuracy of other maps, but names all the 'paden' (paths) outside the city walls that disappeared shortly after with the expansions of the city.

The file [map-hondius-1612.csv](map-hondius-1612.csv) lists all the streetnames on the map and identifies them with an Adamlink URI.


