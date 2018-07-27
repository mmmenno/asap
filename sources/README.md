# ASAP Sources

## BAG buildings construction years

The Basisadministratie Adressen en Gebouwen (BAG) contains every building in the Netherlands. The construction dates of buildings are not always very thrustworthy - lots of buildings are said to be built in 1005, meaning, I guess, something like "probably rather old".

The BAG can be accessed through a [SPARQL endpoint](https://data.pdok.nl/sparql#), in which we used the following query:

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
