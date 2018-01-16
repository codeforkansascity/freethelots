# Free The Lots
A data-processing and analysis tool designed to help lawyers identify and clear mortgages, judgment liens and other claims clouding titles of abandoned properties owned by the Land Bank of Kansas City. Without clear titles, prospective property owners generally cannot get loans to rehab the properties and cannot sell the properties. Outstanding claims stand as major obstacles preventing hundreds of urban core homes from being rehabbed and turned into good quality housing for low-income families.

Jackson County Recorder of Deeds property data is available on-line but sub-searches are not possible.  So, for example, we can search for all Land Bank properties and we can search for all Deutsche Bank mortgages in Jackson County, but we cannot cross-reference the two searches.  It would take hundreds of hours to manually complete a cross-search.  This keeps us from bringing a single lawsuit to clear all mortgages of major lenders on Land Bank properties.  We are left bringing claims one property at a time, which is relatively inefficient. 


## Problem Statement
Kansas City has a problem with vacant and abandoned properties. It’s not that properties are undesirable—it’s that vacant properties often have old mortgages, liens or other claims against them that prevent them from being developed. The Land Bank of Kansas City has more than 4,000 vacant properties.  Most of the properties have legal claims against them. Neighborhood Legal Support (NLS) of Kansas City is a not-for-profit law firm that clears legal claims from abandoned and vacant properties, like Land Bank properties, so the properties can be sold and turned into good quality housing for families. Currently NLS clears title on properties one house at a time. Even if a lienholder, like Deutsche Bank, has mortgages against 80 Land Bank properties, there is currently no way to search the Recorder of Deeds database to determine the Land Bank properties that Deutsche Bank has mortgages against and efficiently clear all 80 of them.

## Key Feature Statement
We’re developing a tool with clean, searchable Recorder of Deeds data to identify all Land Bank properties with current claims by large lienholders.  

## Stakeholders
The champion for this project is **Gregg Lombardi** of **Neighborhood Legal Support**, who pitched the project at Hack KC 2017. NLS is active in clearing property titles and Gregg, NLS’s founding executive director, has been working on a weekly basis with the project team.

Other potential users for the tool include **Legal Aid of Western Missouri Union**, the **Dentons Law Firm**, which is clearing titles on Land Bank properties on a pro bono basis, the **UMKC Law School’s Clear Title Clinic**, and the **Land Bank** itself, all of whom (except Dentons) work with Code for KC on other projects.

Indirect stakeholders who stand to benefit from this work include neighborhood associations, people who live in the urban core, and Jackson County government.

# How We Can Help Reform the Tax Sale Process

We can make the problem more visible by providing statistical analysis of the number of delinquent mortgages outstanding on properties sold through the Jackson county tax sale process.  We can also  provide a sample estimate of the average amount of debt per property in order to help push the county to give the legally required notice of its tax sales.

Banks would be another indirect beneficiary.  If the county improves its tax sale system to give proper notice, more properties would sell and would sell at a higher price.  Surplus funds would go to banks for delinquent mortgages on the properties.  

More properties would also be rehabilitated, which would make the urban core a safer and better place to live.

## Approach 
NLS has purchased all county property data from the Jackson County Recorder of Deeds. Work to date has included cleaning the data, creating a wireframe and a website to post the data once the search has been completed. 

We have reduced the data to include only Land Bank Properties, and we will narrow the search to only properties that the Land Bank currently owns; search for all current claims against each Land Bank property; and produce an easily searchable Excel spreadsheet showing all current claims, by claimant, against all Land Bank properties.  This is the Minimum Viable Product (MVP).

The Land Bank only receives new properties once per year, so the database and search will only need to be updated once per year.

## Road Map

* Acquire data from Jackson County - Done
* Extract, Load, Translate data from Jackson County,Splitting legal descriptions out to make it easier to create search queries (Issue #10)
* Front end design - mostly done
* Code data structures into models for both front and backend
* Define and create queries for front end, determine filters and search boxes, Search by Claimant
* Finalize search by integrating front end query with backend API and logic
* Document load process and automate as much as possible
* Testing
  * Unit Testing
  * Acceptance testing
* Setup hosting
* Roll out to partners
* Presentation user and technical (March)




## Project Team
* Gregg Lombardi - project lead, NLS.  Gregg can be reached through Slack or at gregg@nls4kc.org
* Jacob Hayes - project manager; data cleaning; and search system engineer
* Matt Coleman - data cleaning; main backend dev
* Reggie Brown - website design
* John Kary-- Project design and management consulting
* Katie Killen – assist Gregg with project leadership, NLS
* Josh Moreno - project management and business analysis

## How to Contribute / Call to Action
Slots are taken but to see what we are doing:

* Team uses Waffle.io for project management
* Project communication on the #freethelots Slack channel
* To accommodate our volunteers’ schedule, this project generally meets on Tuesday evenings, roughly once per month.  Check Slack for meeting times and locations

## Skills
The team we have has all the necessary skills.  We have project managers, business analysts, front-end engineering, back-end engineering, and designers


## Technologies
* PostGis
* Laravel
* Vue2
* Bootstrap
* jQuery

## How to contribute 
Currently filled up.

